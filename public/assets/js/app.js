document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
    button.addEventListener('click', () => sidebar?.classList.toggle('show'));
  });

  const saleForm = document.querySelector('[data-sale-form]');
  if (!saleForm) return;

  const productSelect = saleForm.querySelector('[data-product-select]');
  const productSearch = saleForm.querySelector('[data-product-search]');
  const qtyInput = saleForm.querySelector('[data-quantity]');
  const addButton = saleForm.querySelector('[data-add-item]');
  const rows = saleForm.querySelector('[data-sale-rows]');
  const empty = saleForm.querySelector('[data-sale-empty]');
  const itemsJson = saleForm.querySelector('[name="items_json"]');
  const discountInput = saleForm.querySelector('[name="discount"]');
  const paidInput = saleForm.querySelector('[name="amount_paid"]');
  const subtotalEl = saleForm.querySelector('[data-subtotal]');
  const totalEl = saleForm.querySelector('[data-total]');
  const balanceEl = saleForm.querySelector('[data-balance]');
  const cart = [];
  const initialProductOptions = productSelect.innerHTML;

  const moneyFormatter = new Intl.NumberFormat(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, (char) => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;',
    }[char]));
  }

  function selectedProduct() {
    const option = productSelect.options[productSelect.selectedIndex];
    if (!option || !option.value) return null;
    return {
      id: Number(option.value),
      code: option.dataset.code,
      name: option.dataset.name,
      price: Number(option.dataset.price),
      quantity: Number(option.dataset.quantity),
      unit: option.dataset.unit || 'pcs',
    };
  }

  function setProductOptions(products) {
    productSelect.innerHTML = '';
    const first = document.createElement('option');
    first.value = '';
    first.textContent = products.length ? 'Select product' : 'No matching active products';
    productSelect.appendChild(first);

    products.forEach((product) => {
      const option = document.createElement('option');
      option.value = product.id;
      option.dataset.code = product.product_code;
      option.dataset.name = product.product_name;
      option.dataset.price = product.selling_price;
      option.dataset.quantity = product.quantity;
      option.dataset.unit = product.unit || 'pcs';
      option.textContent = `${product.product_code} - ${product.product_name} (${product.quantity} available)`;
      productSelect.appendChild(option);
    });
  }

  function render() {
    rows.innerHTML = '';
    empty.classList.toggle('d-none', cart.length > 0);

    cart.forEach((item, index) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>
          <strong>${escapeHtml(item.name)}</strong>
          <span class="d-block text-muted small">${escapeHtml(item.code)}</span>
        </td>
        <td>${item.available} ${escapeHtml(item.unit)}</td>
        <td><input class="form-control form-control-sm" type="number" min="1" max="${item.available}" value="${item.quantity}" data-row-qty="${index}"></td>
        <td>${moneyFormatter.format(item.price)}</td>
        <td>${moneyFormatter.format(item.price * item.quantity)}</td>
        <td><button class="btn btn-sm btn-outline-danger" type="button" data-remove-item="${index}" title="Remove item"><i class="bi bi-trash"></i></button></td>
      `;
      rows.appendChild(tr);
    });

    const subtotal = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const discount = Math.max(0, Number(discountInput.value || 0));
    const total = Math.max(0, subtotal - discount);
    const paid = Math.max(0, Number(paidInput.value || 0));
    const balance = Math.max(0, total - paid);

    subtotalEl.textContent = moneyFormatter.format(subtotal);
    totalEl.textContent = moneyFormatter.format(total);
    balanceEl.textContent = moneyFormatter.format(balance);
    itemsJson.value = JSON.stringify(cart.map((item) => ({ product_id: item.id, quantity: item.quantity })));
  }

  addButton.addEventListener('click', () => {
    const product = selectedProduct();
    const quantity = Number(qtyInput.value || 0);
    if (!product || quantity < 1 || quantity > product.quantity) {
      saleForm.querySelector('[data-sale-message]').textContent = product
        ? `Enter a quantity between 1 and ${product.quantity}.`
        : 'Select a product first.';
      return;
    }

    const existing = cart.find((item) => item.id === product.id);
    if (existing) {
      const nextQuantity = existing.quantity + quantity;
      if (nextQuantity > existing.available) {
        saleForm.querySelector('[data-sale-message]').textContent = `Only ${existing.available} units are available.`;
        return;
      }
      existing.quantity = nextQuantity;
    } else {
      cart.push({
        id: product.id,
        code: product.code,
        name: product.name,
        price: product.price,
        quantity,
        available: product.quantity,
        unit: product.unit,
      });
    }

    saleForm.querySelector('[data-sale-message]').textContent = '';
    qtyInput.value = 1;
    render();
  });

  if (productSearch) {
    let searchTimer;
    productSearch.addEventListener('input', () => {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(async () => {
        const term = productSearch.value.trim();
        if (term.length < 2) {
          productSelect.innerHTML = initialProductOptions;
          return;
        }

        try {
          const separator = productSearch.dataset.searchUrl.includes('?') ? '&' : '?';
          const response = await fetch(`${productSearch.dataset.searchUrl}${separator}q=${encodeURIComponent(term)}`, {
            headers: { Accept: 'application/json' },
          });
          if (!response.ok) return;
          const payload = await response.json();
          setProductOptions(payload.products || []);
        } catch (error) {
          productSelect.innerHTML = initialProductOptions;
        }
      }, 250);
    });
  }

  rows.addEventListener('input', (event) => {
    const target = event.target.closest('[data-row-qty]');
    if (!target) return;
    const index = Number(target.dataset.rowQty);
    const item = cart[index];
    item.quantity = Math.min(item.available, Math.max(1, Number(target.value || 1)));
    render();
  });

  rows.addEventListener('click', (event) => {
    const button = event.target.closest('[data-remove-item]');
    if (!button) return;
    cart.splice(Number(button.dataset.removeItem), 1);
    render();
  });

  discountInput.addEventListener('input', render);
  paidInput.addEventListener('input', render);
  saleForm.addEventListener('submit', (event) => {
    if (cart.length === 0) {
      event.preventDefault();
      saleForm.querySelector('[data-sale-message]').textContent = 'Add at least one product before saving.';
    }
  });

  render();
});

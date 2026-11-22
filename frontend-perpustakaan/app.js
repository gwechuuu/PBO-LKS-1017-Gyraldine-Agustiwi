const API_BASE = 'http://127.0.0.1:8000/api';

let allBooks = [];
let allCategories = [];

const bookForm = document.getElementById('bookForm');
const bookFormTitle = document.getElementById('bookFormTitle');
const bookIdInput = document.getElementById('bookId');
const judulInput = document.getElementById('judul');
const penulisInput = document.getElementById('penulis');
const categoryIdSelect = document.getElementById('categoryId');
const tahunInput = document.getElementById('tahun');
const stokInput = document.getElementById('stok');
const btnBookSubmit = document.getElementById('btnBookSubmit');
const btnBookCancel = document.getElementById('btnBookCancel');

const categoryForm = document.getElementById('categoryForm');
const categoryFormTitle = document.getElementById('categoryFormTitle');
const categoryEditIdInput = document.getElementById('categoryEditId');
const categoryNameInput = document.getElementById('categoryName');
const categoryDescInput = document.getElementById('categoryDesc');
const btnCategorySubmit = document.getElementById('btnCategorySubmit');
const btnCategoryCancel = document.getElementById('btnCategoryCancel');
const categoryListEl = document.getElementById('categoryList');

const searchInput = document.getElementById('searchInput');
const btnRefreshAll = document.getElementById('btnRefreshAll');
const bookTableBody = document.getElementById('bookTableBody');
const bookCount = document.getElementById('bookCount');
const alertBox = document.getElementById('alertBox');

function showAlert(message, type = 'success') {
  alertBox.textContent = message;
  alertBox.className = `alert ${type}`;
}

function hideAlert() {
  alertBox.className = 'alert hidden';
  alertBox.textContent = '';
}

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function getCategoryName(book) {
  if (book.category && book.category.name) {
    return book.category.name;
  }
  const catId = book.category_id ?? book.categoryId;
  if (!catId) return '-';
  const found = allCategories.find((c) => String(c.id) === String(catId));
  return found ? found.name : `#${catId}`;
}

function resetBookForm() {
  bookForm.reset();
  bookIdInput.value = '';
  categoryIdSelect.value = '';
  bookFormTitle.textContent = 'Tambah Buku Baru';
  btnBookSubmit.textContent = 'Simpan Buku';
  btnBookCancel.classList.add('hidden');
}

function fillBookFormForEdit(book) {
  hideAlert();
  bookIdInput.value = book.id;
  judulInput.value = book.judul ?? '';
  penulisInput.value = book.penulis ?? '';
  categoryIdSelect.value = book.category_id ?? book.categoryId ?? '';
  tahunInput.value = book.tahun ?? '';
  stokInput.value = book.stok ?? '';
  bookFormTitle.textContent = `Edit Buku #${book.id}`;
  btnBookSubmit.textContent = 'Update Buku';
  btnBookCancel.classList.remove('hidden');
  judulInput.focus();
}

function resetCategoryForm() {
  categoryForm.reset();
  categoryEditIdInput.value = '';
  categoryFormTitle.textContent = 'Tambah Kategori';
  btnCategorySubmit.textContent = 'Simpan Kategori';
  btnCategoryCancel.classList.add('hidden');
}

function fillCategoryFormForEdit(cat) {
  hideAlert();
  categoryEditIdInput.value = cat.id;
  categoryNameInput.value = cat.name ?? '';
  categoryDescInput.value = cat.description ?? '';
  categoryFormTitle.textContent = `Edit Kategori #${cat.id}`;
  btnCategorySubmit.textContent = 'Update Kategori';
  btnCategoryCancel.classList.remove('hidden');
  categoryNameInput.focus();
}

function renderCategorySelect() {
  const selectedVal = categoryIdSelect.value;
  categoryIdSelect.innerHTML = [
    '<option value="">-- Tanpa Kategori --</option>',
    ...allCategories.map(
      (cat) => `<option value="${escapeHtml(cat.id)}">${escapeHtml(cat.name)}</option>`
    ),
  ].join('');
  categoryIdSelect.value = selectedVal;
}

function renderCategoryList() {
  if (!Array.isArray(allCategories) || allCategories.length === 0) {
    categoryListEl.innerHTML = '<li class="empty">Belum ada kategori.</li>';
    return;
  }

  categoryListEl.innerHTML = allCategories.map((cat) => `
    <li class="category-item">
      <div class="category-meta">
        <span class="category-name">${escapeHtml(cat.name)}</span>
        ${cat.description ? `<span class="category-desc">${escapeHtml(cat.description)}</span>` : ''}
      </div>
      <div class="actions-cell">
        <button type="button" class="btn btn-edit" data-cat-action="edit" data-id="${escapeHtml(cat.id)}">Edit</button>
        <button type="button" class="btn btn-delete" data-cat-action="delete" data-id="${escapeHtml(cat.id)}">Hapus</button>
      </div>
    </li>
  `).join('');
}

function renderBooks() {
  const keyword = searchInput.value.trim().toLowerCase();
  const filtered = keyword
    ? allBooks.filter((b) =>
        String(b.judul ?? '').toLowerCase().includes(keyword) ||
        String(b.penulis ?? '').toLowerCase().includes(keyword)
      )
    : allBooks;

  bookCount.textContent = `${filtered.length} buku`;

  if (!Array.isArray(filtered) || filtered.length === 0) {
    bookTableBody.innerHTML = '<tr><td colspan="7" class="empty">Data buku tidak ditemukan.</td></tr>';
    return;
  }

  bookTableBody.innerHTML = filtered.map((book) => `
    <tr>
      <td>${escapeHtml(book.id)}</td>
      <td>${escapeHtml(book.judul)}</td>
      <td>${escapeHtml(book.penulis)}</td>
      <td>${escapeHtml(getCategoryName(book))}</td>
      <td>${escapeHtml(book.tahun)}</td>
      <td>${escapeHtml(book.stok)}</td>
      <td>
        <div class="actions-cell">
          <button type="button" class="btn btn-edit" data-action="edit" data-id="${escapeHtml(book.id)}">Edit</button>
          <button type="button" class="btn btn-delete" data-action="delete" data-id="${escapeHtml(book.id)}">Hapus</button>
        </div>
      </td>
    </tr>
  `).join('');
}

async function fetchCategories() {
  try {
    const response = await fetch(`${API_BASE}/categories`);
    const result = await response.json();
    if (!response.ok) {
      throw new Error(result.message || 'Gagal memuat kategori');
    }
    allCategories = result.data || [];
    renderCategorySelect();
    renderCategoryList();
    renderBooks();
  } catch (error) {
    categoryListEl.innerHTML = '<li class="empty">Kategori belum tersedia di API.</li>';
  }
}

async function fetchBooks() {
  try {
    const response = await fetch(`${API_BASE}/books`);
    const result = await response.json();
    if (!response.ok) {
      throw new Error(result.message || 'Gagal memuat daftar buku');
    }
    allBooks = result.data || [];
    renderBooks();
  } catch (error) {
    bookTableBody.innerHTML = '<tr><td colspan="7" class="empty">Gagal terhubung ke API buku.</td></tr>';
    showAlert(error.message || 'Terjadi kesalahan saat mengambil data', 'error');
  }
}

bookForm.addEventListener('submit', async (event) => {
  event.preventDefault();
  hideAlert();

  const id = bookIdInput.value.trim();
  const payload = {
    judul: judulInput.value.trim(),
    penulis: penulisInput.value.trim(),
    tahun: Number(tahunInput.value),
    stok: Number(stokInput.value),
  };

  if (categoryIdSelect.value !== '') {
    payload.category_id = Number(categoryIdSelect.value);
  }

  const isEdit = id !== '';
  const url = isEdit ? `${API_BASE}/books/${id}` : `${API_BASE}/books`;
  const method = isEdit ? 'PUT' : 'POST';

  try {
    const response = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const result = await response.json();

    if (!response.ok) {
      const detail = result.errors
        ? Object.values(result.errors).flat().join(' | ')
        : (result.message || 'Validasi gagal');
      throw new Error(detail);
    }

    showAlert(result.message || (isEdit ? 'Buku berhasil diperbarui' : 'Buku berhasil ditambahkan'), 'success');
    resetBookForm();
    await fetchBooks();
  } catch (error) {
    showAlert(error.message || 'Gagal menyimpan data buku', 'error');
  }
});

categoryForm.addEventListener('submit', async (event) => {
  event.preventDefault();
  hideAlert();

  const editId = categoryEditIdInput.value.trim();
  const payload = {
    name: categoryNameInput.value.trim(),
    description: categoryDescInput.value.trim() || null,
  };

  const isEdit = editId !== '';
  const url = isEdit ? `${API_BASE}/categories/${editId}` : `${API_BASE}/categories`;
  const method = isEdit ? 'PUT' : 'POST';

  try {
    const response = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const result = await response.json();

    if (!response.ok) {
      const detail = result.errors
        ? Object.values(result.errors).flat().join(' | ')
        : (result.message || 'Gagal menyimpan kategori');
      throw new Error(detail);
    }

    showAlert(result.message || (isEdit ? 'Kategori diperbarui' : 'Kategori ditambahkan'), 'success');
    resetCategoryForm();
    await fetchCategories();
  } catch (error) {
    showAlert(error.message || 'Gagal menyimpan kategori', 'error');
  }
});

categoryListEl.addEventListener('click', async (event) => {
  const button = event.target.closest('button[data-cat-action]');
  if (!button) return;

  const action = button.dataset.catAction;
  const id = button.dataset.id;

  if (action === 'edit') {
    const found = allCategories.find((c) => String(c.id) === String(id));
    if (found) fillCategoryFormForEdit(found);
    return;
  }

  if (action === 'delete') {
    if (!confirm(`Hapus kategori #${id}?`)) return;
    hideAlert();
    try {
      const response = await fetch(`${API_BASE}/categories/${id}`, { method: 'DELETE' });
      const result = await response.json();
      if (!response.ok) {
        throw new Error(result.message || 'Gagal menghapus kategori');
      }
      if (categoryEditIdInput.value === id) resetCategoryForm();
      showAlert(result.message || 'Kategori dihapus', 'success');
      await fetchCategories();
    } catch (error) {
      showAlert(error.message, 'error');
    }
  }
});

bookTableBody.addEventListener('click', async (event) => {
  const button = event.target.closest('button[data-action]');
  if (!button) return;

  const action = button.dataset.action;
  const id = button.dataset.id;

  if (action === 'edit') {
    try {
      const response = await fetch(`${API_BASE}/books/${id}`);
      const result = await response.json();
      if (!response.ok || !result.data) {
        throw new Error(result.message || 'Data buku tidak ditemukan');
      }
      fillBookFormForEdit(result.data);
    } catch (error) {
      showAlert(error.message, 'error');
    }
    return;
  }

  if (action === 'delete') {
    if (!confirm(`Hapus buku dengan ID #${id}?`)) return;
    hideAlert();
    try {
      const response = await fetch(`${API_BASE}/books/${id}`, { method: 'DELETE' });
      const result = await response.json();
      if (!response.ok) {
        throw new Error(result.message || 'Gagal menghapus buku');
      }
      if (bookIdInput.value === id) resetBookForm();
      showAlert(result.message || 'Buku berhasil dihapus', 'success');
      await fetchBooks();
    } catch (error) {
      showAlert(error.message, 'error');
    }
  }
});

searchInput.addEventListener('input', renderBooks);
btnBookCancel.addEventListener('click', () => { hideAlert(); resetBookForm(); });
btnCategoryCancel.addEventListener('click', () => { hideAlert(); resetCategoryForm(); });
btnRefreshAll.addEventListener('click', async () => {
  hideAlert();
  await Promise.all([fetchCategories(), fetchBooks()]);
});

(async () => {
  await Promise.all([fetchCategories(), fetchBooks()]);
})();
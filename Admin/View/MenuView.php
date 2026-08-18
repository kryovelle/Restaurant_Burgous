<?php
require_once __DIR__ . "/GlobalView.php";

class MenuView extends GlobalView {

    function content($mark, $items, $categories){
        ?>
<body>

<div class="admin-shell">
    <?php echo $this->sideBar($mark); ?>

    <main class="admin-main">

        <div class="page-head-row">
            <h1>Menu Management</h1>
            <div class="page-head-actions">
                <button class="btn-outline" onclick="openCategoriesModal()">Manage Categories</button>
                <button class="btn-solid" onclick="openAddItemModal()">+ Add Item</button>
            </div>
        </div>

        <div class="filter-row">
            <button class="filter-chip active" data-filter="all" onclick="setFilter('all')">All</button>
            <?php foreach ($categories as $cat): ?>
                <button class="filter-chip" data-filter="<?php echo $cat['id']; ?>" onclick="setFilter('<?php echo $cat['id']; ?>')">
                    <?php echo htmlspecialchars($cat['title']); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="items-panel">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Available</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="itemsTableBody">
                    <?php if (empty($items)): ?>
                        <tr><td colspan="6" style="text-align:center; color:var(--muted); padding:30px;">No menu items yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <?php
                                $cat = array_values(array_filter($categories, fn($c) => $c['id'] == $item['category']));
                                $catTitle = $cat ? $cat[0]['title'] : 'Uncategorized';
                            ?>
                            <tr data-category="<?php echo $item['category']; ?>">
                                <td><img class="item-photo" src="/images/<?php echo htmlspecialchars($item['photo_url'] ?: '/images/placeholder.jpg'); ?>" alt=""></td>
                                <td class="item-name"><?php echo htmlspecialchars($item['name']); ?></td>
                                <td class="item-price">$<?php echo htmlspecialchars($item['price']); ?></td>
                                <td><span class="cat-badge"><?php echo htmlspecialchars($catTitle); ?></span></td>
                                <td><span class="avail-dot <?php echo $item['is_available'] ? 'yes' : 'no'; ?>"></span></td>
                                <td>
                                    <div class="row-actions">
                                        <button onclick="viewItem(<?php echo $item['id']; ?>)">View</button>
                                        <button onclick="editItem(<?php echo $item['id']; ?>)">Edit</button>
                                        <button class="danger" onclick="deleteItem(<?php echo $item['id']; ?>)">Del</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</div>

<!-- MODAL ROOT — filled dynamically by JS -->
<div id="modalRoot"></div>

<!-- Hidden template data for JS (categories only — items are already in the table above) -->
<script>
    const categories = <?php echo json_encode($categories); ?>;

let currentlyEditingCat = null;

/* ---------- HELPERS ---------- */

function escapeHtml(str){
  const div = document.createElement('div');
  div.textContent = str ?? '';
  return div.innerHTML;
}

function openModal(html){
  const root = document.getElementById('modalRoot');
  root.innerHTML = html;
  root.classList.add('show');
}

function closeModal(){
  const root = document.getElementById('modalRoot');
  root.classList.remove('show');
  root.innerHTML = '';
  currentlyEditingCat = null;
}

/* ---------- CATEGORY FILTER (operates on the PHP-rendered table rows) ---------- */

function setFilter(filterId){
  document.querySelectorAll('.filter-chip').forEach(chip => {
    chip.classList.toggle('active', chip.dataset.filter === filterId.toString());
  });

  document.querySelectorAll('#itemsTableBody tr[data-category]').forEach(row => {
    const show = (filterId === 'all') || (row.dataset.category === filterId.toString());
    row.style.display = show ? '' : 'none';
  });
}

/* ---------- ITEM MODAL (view / edit / add) ---------- */

function renderItemModal(item, mode){
  // mode: 'view' | 'edit' | 'add'
  const disabled = mode === 'view' ? 'disabled' : '';
  const title = mode === 'add' ? 'Add New Item' : (mode === 'edit' ? 'Edit Item' : item.name);

  const categoryOptions = categories.map(c =>
    `<option value="${c.id}" ${item && item.category == c.id ? 'selected' : ''}>${escapeHtml(c.title)}</option>`
  ).join('');

  const formAction = mode === 'add' ? 'addMenuItem' : 'editMenuItem';

  const html = `
    <div class="modal-box">
      <button class="modal-close" onclick="closeModal()">&times;</button>
      <h2>${escapeHtml(title)}</h2>

      <img class="item-modal-photo" src="/images/${(item && item.photo_url) || '/images/placeholder.jpg'}" alt="">

      ${mode === 'view' ? `
        <div class="form-row"><label>Name</label><input type="text" value="${escapeHtml(item.name)}" disabled></div>
        <div class="form-row"><label>Description</label><textarea disabled>${escapeHtml(item.description)}</textarea></div>
        <div class="form-row"><label>Price</label><input type="text" value="${escapeHtml(item.price)}" disabled></div>
        <div class="form-row"><label>Category</label><select disabled>${categoryOptions}</select></div>
        <div class="form-row"><label>Recommended</label><input type="text" value="${item.is_recommended ? 'Yes' : 'No'}" disabled></div>
        <div class="form-row"><label>Available</label><input type="text" value="${item.is_available ? 'Yes' : 'No'}" disabled></div>

        <div class="modal-actions">
          <button class="btn-solid-sm" onclick="editItem(${item.id})">Edit</button>
          <button class="btn-outline-sm" onclick="closeModal()">Close</button>
        </div>
      ` : `
        <form method="post" action="/Admin/redirect.php" enctype="multipart/form-data">
          <input type="hidden" name="${formAction}" value="1">
          ${mode === 'edit' ? `<input type="hidden" name="id" value="${item.id}">` : ''}

           <div class="form-row">
            <label>Photo</label>
            <input type="file" name="photo" accept="image/*">
          </div>
          <div class="form-row">
            <label>Name</label>
            <input type="text" name="name" value="${item ? escapeHtml(item.name) : ''}" required>
          </div>
          <div class="form-row">
            <label>Description</label>
            <textarea name="description">${item ? escapeHtml(item.description) : ''}</textarea>
          </div>
          <div class="form-row">
            <label>Price</label>
            <input type="number" step="0.01" name="price" value="${item ? item.price : ''}" required>
          </div>
          <div class="form-row">
            <label>Category</label>
            <select name="category">${categoryOptions}</select>
          </div>
          <div class="form-row">
            <label>Recommended</label>
            <select name="is_recommended">
              <option value="1" ${item && item.is_recommended == 1 ? 'selected' : ''}>Yes</option>
              <option value="0" ${item && item.is_recommended == 0 ? 'selected' : ''}>No</option>
            </select>
          </div>
          <div class="form-row">
            <label>Available</label>
            <select name="is_available">
              <option value="1" ${item && item.is_available == 1 ? 'selected' : ''}>Yes</option>
              <option value="0" ${item && item.is_available == 0 ? 'selected' : ''}>No</option>
            </select>
          </div>

           

          <div class="modal-actions">
            <button type="submit" class="btn-solid-sm">Save</button>
            <button type="button" class="btn-outline-sm" onclick="closeModal()">Cancel</button>
            ${mode === 'edit' ? `<button type="button" class="btn-danger-sm" onclick="deleteItem(${item.id})">Delete</button>` : ''}
          </div>
        </form>
      `}
    </div>
  `;

  openModal(html);
}

async function viewItem(id){
  const res = await fetch(`/Admin/Menu/getItem?id=${id}`);
  const item = await res.json();
  renderItemModal(item, 'view');
}

async function editItem(id){
  const res = await fetch(`/Admin/Menu/getItem?id=${id}`);
  const item = await res.json();
  renderItemModal(item, 'edit');
}

function openAddItemModal(){
  renderItemModal(null, 'add');
}

function deleteItem(id){
  if (!confirm('Delete this menu item?')) return;

  const form = document.createElement('form');
  form.method = 'post';
  form.action = '/Admin/redirect.php';
  form.innerHTML = `
    <input type="hidden" name="deleteMenuItem" value="1">
    <input type="hidden" name="id" value="${id}">
  `;
  document.body.appendChild(form);
  form.submit();
}

/* ---------- CATEGORIES MODAL ---------- */

function renderCatRow(cat){
  return `
    <div class="cat-row" data-cat-id="${cat.id}">
      <div class="cat-row-display">
        <div class="cat-row-top">
          <span class="cat-row-title">${escapeHtml(cat.title)}</span>
          <div class="cat-row-actions">
            <button class="btn-link" onclick="editCategory(${cat.id})">Edit</button>
            <button class="btn-link danger" onclick="deleteCategory(${cat.id})">Del</button>
          </div>
        </div>
        <div class="cat-row-desc">${escapeHtml(cat.description)}</div>
      </div>

      <form class="cat-row-edit" method="post" action="/Admin/redirect.php" style="display:none;">
        <input type="hidden" name="editCategory" value="1">
        <input type="hidden" name="id" value="${cat.id}">
        <div class="form-row">
          <label>Title</label>
          <input type="text" name="title" value="${escapeHtml(cat.title)}" required>
        </div>
        <div class="form-row">
          <label>Description</label>
          <input type="text" name="description" value="${escapeHtml(cat.description)}">
        </div>
        <div class="form-row">
          <label>Icon</label>
          <input type="text" name="icon" value="${escapeHtml(cat.icon)}">
        </div>
        <div class="cat-row-actions">
          <button type="submit" class="btn-solid-sm">Save</button>
          <button type="button" class="btn-outline-sm" onclick="cancelEditCategory(${cat.id})">Cancel</button>
        </div>
      </form>
    </div>
  `;
}

function renderCategoriesModal(){
  const html = `
    <div class="modal-box">
      <button class="modal-close" onclick="closeModal()">&times;</button>
      <h2>Manage Categories</h2>

      <div class="cat-list" id="catList">
        ${categories.map(cat => renderCatRow(cat)).join('')}
      </div>

      <div class="cat-add-section">
        <h3>Add New Category</h3>
        <form method="post" action="/Admin/redirect.php">
          <input type="hidden" name="addCategory" value="1">
          <div class="form-row">
            <label>Title</label>
            <input type="text" name="title" placeholder="e.g. Starters" required>
          </div>
          <div class="form-row">
            <label>Description</label>
            <input type="text" name="description" placeholder="e.g. Light bites to open the meal">
          </div>
          <div class="form-row">
            <label>Icon</label>
            <input type="text" name="icon" placeholder="e.g. starter">
          </div>
          <button type="submit" class="btn-solid" style="width:100%;">+ Add Category</button>
        </form>
      </div>
    </div>
  `;
  openModal(html);
}

function openCategoriesModal(){
  renderCategoriesModal();
}

function editCategory(id){
  if (currentlyEditingCat !== null && currentlyEditingCat !== id){
    cancelEditCategory(currentlyEditingCat);
  }
  const row = document.querySelector(`.cat-row[data-cat-id="${id}"]`);
  row.querySelector('.cat-row-display').style.display = 'none';
  row.querySelector('.cat-row-edit').style.display = 'block';
  currentlyEditingCat = id;
}

function cancelEditCategory(id){
  const row = document.querySelector(`.cat-row[data-cat-id="${id}"]`);
  row.querySelector('.cat-row-display').style.display = 'block';
  row.querySelector('.cat-row-edit').style.display = 'none';
  currentlyEditingCat = null;
}

function deleteCategory(id){
  if (!confirm('Delete this category? Items using it will need to be reassigned.')) return;

  const form = document.createElement('form');
  form.method = 'post';
  form.action = '/Admin/redirect.php';
  form.innerHTML = `
    <input type="hidden" name="deleteCategory" value="1">
    <input type="hidden" name="id" value="${id}">
  `;
  document.body.appendChild(form);
  form.submit();
}

</script>

        <?php
    }

    function displayMenuView($items, $categories, $mark ){
        $this->header($mark);
        $this->content($mark, $items, $categories);
        $this->scirptSideBar();
    }
}
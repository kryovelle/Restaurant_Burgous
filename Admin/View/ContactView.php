<?php
require_once __DIR__ . "/GlobalView.php";

class ContactView extends GlobalView {

    function content($mark, $messages){
        ?>
<body>

<div class="admin-shell">
    <?php $this->sideBar($mark); ?>

    <main class="admin-main">

        <div class="page-head-row">
            <h1>Contact Messages</h1>
        </div>

        <div class="res-filter-bar">
            <select id="filterStatus">
                <option value="">Status: All</option>
                <option value="unread">Unread</option>
                <option value="read">Read</option>
            </select>
            <span class="res-filter-sort">Sorted by Date ↓</span>
        </div>

        <div class="res-panel">
            <table class="res-table" id="contactTable">
                <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)): ?>
                        <tr><td colspan="6" style="text-align:center; color:var(--muted); padding:30px;">No messages yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <tr data-status="<?php echo $msg['is_read'] ? 'read' : 'unread'; ?>">
                                <td><?php if (!$msg['is_read']): ?><span class="unread-dot"></span><?php endif; ?></td>
                                <td class="<?php echo !$msg['is_read'] ? 'unread-name' : ''; ?>"><?php echo htmlspecialchars($msg['name']); ?></td>
                                <td><a class="email-link" href="mailto:<?php echo htmlspecialchars($msg['email']); ?>"><?php echo htmlspecialchars($msg['email']); ?></a></td>
                                <td><?php echo htmlspecialchars($msg['phone_number'] ?? '—'); ?></td>
                                <td><?php echo htmlspecialchars(date('m/d/Y', strtotime($msg['created_at']))); ?></td>
                                <td>
                                    <div class="row-actions">
                                        <button onclick="viewMessage(<?php echo $msg['id']; ?>)">View</button>
                                        <button class="danger" onclick="confirmDeleteMessage(<?php echo $msg['id']; ?>, '<?php echo htmlspecialchars($msg['name'], ENT_QUOTES); ?>')">Del</button>
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

<div id="modalRoot"></div>

<script>

  async function refreshContact(){
  try {
    const res = await fetch('/Admin/Contact/?format=json', { cache: 'no-store' });
    if (!res.ok) throw new Error('Request failed: ' + res.status);
    const data = await res.json();

    renderContactTable(data.messages);

  } catch (err){
    console.error('Contact refresh failed:', err);
  }
}

function renderContactTable(messages){
  const tbody = document.querySelector('#contactTable tbody');

  if (!messages || messages.length === 0){
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:var(--muted); padding:30px;">No messages yet.</td></tr>`;
    return;
  }

  tbody.innerHTML = messages.map(msg => `
    <tr data-id="${msg.id}" data-status="${msg.is_read ? 'read' : 'unread'}">
      <td>${!msg.is_read ? '<span class="unread-dot"></span>' : ''}</td>
      <td class="${!msg.is_read ? 'unread-name' : ''}">${escapeHtml(msg.name)}</td>
      <td><a class="email-link" href="mailto:${escapeHtml(msg.email)}">${escapeHtml(msg.email)}</a></td>
      <td>${escapeHtml(msg.phone_number || '—')}</td>
      <td>${escapeHtml(msg.created_at)}</td>
      <td>
        <div class="row-actions">
          <button onclick="viewMessage(${msg.id})">View</button>
          <button class="danger" onclick="confirmDeleteMessage(${msg.id}, '${escapeHtml(msg.name)}')">Del</button>
        </div>
      </td>
    </tr>
  `).join('');
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
}

async function viewMessage(id){
  const res = await fetch(`/Admin/Contact/getMessage?id=${id}`);
  const msg = await res.json();

  const html = `
    <div class="modal-box">
      <button class="modal-close" onclick="closeModal()">&times;</button>
      <h2>Message</h2>

      <div class="modal-line"><strong>${escapeHtml(msg.name)}</strong></div>
      <div class="modal-line">${escapeHtml(msg.email)} | ${escapeHtml(msg.phone_number || '—')}</div>
      <div class="modal-line">Received: ${escapeHtml(msg.created_at)}</div>

      <hr class="modal-divider">

      <p class="modal-note">${escapeHtml(msg.message)}</p>

      <div class="modal-actions">
        <button class="btn-danger-sm" onclick="closeModal(); confirmDeleteMessage(${msg.id}, '${escapeHtml(msg.name)}')">Delete</button>
        <button class="btn-outline-sm" onclick="closeModal()">Close</button>
      </div>
    </div>
  `;
  openModal(html);

  // Row is now read — drop the unread dot/bold without waiting for a reload
  const row = document.querySelector(`tr[data-id="${id}"]`);
  if (row){
    row.dataset.status = 'read';
    const dotCell = row.querySelector('.unread-dot');
    if (dotCell) dotCell.remove();
    const nameCell = row.querySelector('.unread-name');
    if (nameCell) nameCell.classList.remove('unread-name');
  }

    refreshContact();
}

function confirmDeleteMessage(id, name){
  const html = `
    <div class="modal-box">
      <button class="modal-close" onclick="closeModal()">&times;</button>
      <h2>Delete Message?</h2>
      <p class="modal-note">This message from <strong>${escapeHtml(name)}</strong> will be permanently deleted.</p>
      <div class="modal-actions">
        <button class="btn-outline-sm" onclick="closeModal()">Cancel</button>
        <button class="btn-danger-sm" onclick="deleteMessage(${id})">Delete</button>
      </div>
    </div>
  `;
  openModal(html);
}

function deleteMessage(id){
  const form = document.createElement('form');
  form.method = 'post';
  form.action = '/Admin/redirect.php';
  form.innerHTML = `
    <input type="hidden" name="deleteContactMessage" value="1">
    <input type="hidden" name="id" value="${id}">
  `;
  document.body.appendChild(form);
  form.submit();
}

function escapeHtml(str){
  const div = document.createElement('div');
  div.textContent = str ?? '';
  return div.innerHTML;
}

document.getElementById('filterStatus').addEventListener('change', function(){
  const filter = this.value;
  document.querySelectorAll('#contactTable tbody tr[data-status]').forEach(row => {
    row.style.display = (!filter || row.dataset.status === filter) ? '' : 'none';
  });
});
</script>

</body>
        <?php
    }

    function displayContactView($mark, $messages){
        $this->header($mark);
        $this->content($mark, $messages);
        $this->scirptSideBar();
    }
}
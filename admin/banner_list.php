<?php
require_once('../process/dbh.php');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id';
$sort_dir = (isset($_GET['sort_dir']) && strtolower($_GET['sort_dir']) === 'asc') ? 'ASC' : 'DESC';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = isset($_GET['per_page']) ? max(5, intval($_GET['per_page'])) : 8;
$offset = ($page - 1) * $per_page;

$allowed_sort = ['id', 'banner_title', 'is_visible', 'is_active'];
if (!in_array($sort_by, $allowed_sort))
    $sort_by = 'id';

$where = "WHERE 1=1";
if ($q !== '') {
    $q_esc = mysqli_real_escape_string($conn, $q);
    $where .= " AND (banner_title LIKE '%$q_esc%')";
}

$countSql = "SELECT COUNT(*) as cnt FROM banners $where";
$cRes = mysqli_query($conn, $countSql);
$cRow = mysqli_fetch_assoc($cRes);
$total = intval($cRow['cnt']);
$total_pages = max(1, ceil($total / $per_page));

$sql = "SELECT * FROM banners $where ORDER BY $sort_by $sort_dir LIMIT $per_page OFFSET $offset";
$res = mysqli_query($conn, $sql);

function build_qs($overrides = [])
{
    $base = $_GET;
    foreach ($overrides as $k => $v)
        $base[$k] = $v;
    return http_build_query($base);
}
?>
<!doctype html>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="../styleview.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <!-- font-awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <meta charset="utf-8">
    <title>Banner Manager | HRMS</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            padding: 20px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .btnAdd {
            background: #007bff;
            color: #fff;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
        }

        .btn {
            padding: 6px 10px;
            border-radius: 5px;
            border: 0;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        img.thumb {
            height: 60px;
            cursor: pointer;
            border-radius: 6px;
            transition: transform .12s;
        }

        img.thumb:hover {
            transform: scale(1.05);
        }

        .controls {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .search {
            padding: 8px 10px;
            width: 260px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .small {
            font-size: 0.9em;
            padding: 6px 8px;
        }

        .toggleBtn {
            cursor: pointer;
            padding: 6px 8px;
            border-radius: 6px;
            border: 1px solid #aaa;
            background: #f6f6f6;
        }

        .activeTag {
            background: #28a745;
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
        }

        /* Lightbox */
        #cinemaOverlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(4px);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        #cinemaOverlay img {
            max-width: 85%;
            max-height: 85%;
            border-radius: 8px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        #cinemaOverlay .meta {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #fff;
            font-weight: 600;
        }

        .pager {
            margin-top: 12px;
            display: flex;
            gap: 6px;
            align-items: center;
            justify-content: flex-end;
        }

        .danger {
            background: #dc3545;
            color: #fff;
        }
    </style>
</head>

<body>

    <header>
        <nav>
            <h1>HRMS</h1>
            <ul id="navli">
                <li><a class="homeblack" href="../aloginwel.php">HOME</a></li>

                <li><a class="homeblack" href="../addemp.php">Add Employee</a></li>
                <li><a class="homeblack" href="../viewemp.php">View Employee</a></li>
                <li><a class="homeblack" href="../assign.php">Assign Project</a></li>
                <li><a class="homeblack" href="../assignproject.php">Project Status</a></li>
                <li><a class="homeblack" href="admin/dashboard.php">Dashboard</a></li>
                <li><a class="homeblack" href="../salaryemp.php">Salary Table</a></li>
                <li><a class="homeblack" href="../empleave.php">Employee Leave</a></li>
                <li><a class="homered" href="../banner_list.php">Banner</a></li>
                <li><a class="homeblack" href="../alogin.html">Log Out</a></li>
            </ul>
        </nav>
    </header>

    <div class="divider"></div>
    <div id="divimg">

    </div>

    <div class="topbar">
        <div>
            <header>
                <h1 style="margin:0;">Banner Manager</h1>
            </header>
            <div style="font-size:0.9em;color:#666;">Total: <?= $total ?> banner(s)</div>
        </div>

        <div style="display:flex;gap:8px;align-items:center;">
            <form method="GET" style="display:flex;gap:8px;align-items:center;">
                <input class="search" name="q" placeholder="Search title..." value="<?= htmlspecialchars($q) ?>">
                <select name="sort_by" class="small">
                    <option value="id" <?= $sort_by === 'id' ? 'selected' : '' ?>>ID</option>
                    <option value="banner_title" <?= $sort_by === 'banner_title' ? 'selected' : '' ?>>Title</option>
                    <option value="is_visible" <?= $sort_by === 'is_visible' ? 'selected' : '' ?>>Visible</option>
                    <option value="is_active" <?= $sort_by === 'is_active' ? 'selected' : '' ?>>Active</option>
                </select>
                <select name="sort_dir" class="small">
                    <option value="desc" <?= $sort_dir === 'DESC' ? 'selected' : '' ?>>Desc</option>
                    <option value="asc" <?= $sort_dir === 'ASC' ? 'selected' : '' ?>>Asc</option>
                </select>
                <select name="per_page" class="small">
                    <option <?= $per_page == 5 ? 'selected' : '' ?> value="5">5</option>
                    <option <?= $per_page == 8 ? 'selected' : '' ?> value="8">8</option>
                    <option <?= $per_page == 12 ? 'selected' : '' ?> value="12">12</option>
                </select>
                <button class="btn" type="submit">Apply</button>
            </form>

            <a class="btnAdd" href="banner_add.php">+ Add Banner</a>
            <a class="reorderBtn" href="banner_reorder.php">↕ Reorder Banners</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Preview</th>
                <th>Title</th>
                <th>Visible</th>
                <th>Active (ordered)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($b = mysqli_fetch_assoc($res)): ?>
                <tr data-id="<?= $b['id'] ?>">
                    <td><?= $b['id'] ?></td>
                    <td><img class="thumb" src="../<?= htmlspecialchars($b['image_url']) ?>" alt=""
                            onclick="openCinema('../<?= htmlspecialchars($b['image_url']) ?>','<?= htmlspecialchars(addslashes($b['banner_title'])) ?>')">
                    </td>
                    <td style="text-align:left;"><?= htmlspecialchars($b['banner_title']) ?></td>
                    <td>
                        <button class="toggleBtn"
                            onclick="toggleVisible(<?= $b['id'] ?>, this)"><?= $b['is_visible'] ? 'Visible' : 'Hidden' ?></button>
                    </td>
                    <td>
                        <?php if ($b['is_active']): ?>
                            <span class="activeTag">ACTIVE</span>
                            <small style="margin-left:6px;color:#666">(#<?= intval($b['active_order'] ?: 0) ?>)</small>
                        <?php endif; ?>
                        <div style="margin-top:6px;">
                            <label style="font-size:0.9em;">
                                <input type="checkbox" name="set_active" value="<?= $b['id'] ?>" <?= $b['is_active'] ? 'checked' : '' ?> onchange="setActive(<?= $b['id'] ?>)">
                                Set Active
                            </label>
                        </div>
                    </td>
                    <td>
                        <div class="controls">
                            <a class="btn" href="banner_edit.php?id=<?= $b['id'] ?>">Edit</a>
                            <button class="btn danger" onclick="confirmDelete(<?= $b['id'] ?>, this)">Delete</button>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="pager">
        <div style="margin-right:auto;">
            Page <?= $page ?> / <?= $total_pages ?>
        </div>
        <?php if ($page > 1): ?>
            <a class="btn" href="?<?= build_qs(['page' => $page - 1]) ?>">Prev</a>
        <?php endif; ?>

        <?php for ($p = 1; $p <= $total_pages; $p++):
            if ($p > $page + 3 || $p < $page - 3) {
                if ($p === 1 || $p === $total_pages) {
                } else
                    continue;
            }
            ?>
            <a class="btn <?= $p === $page ? 'activeTag' : '' ?>" href="?<?= build_qs(['page' => $p]) ?>"><?= $p ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a class="btn" href="?<?= build_qs(['page' => $page + 1]) ?>">Next</a>
        <?php endif; ?>
    </div>

    <div id="cinemaOverlay" onclick="closeCinema(event)">
        <div class="meta" id="cinemaTitle"></div>
        <img id="cinemaImg" src="">
    </div>

    <script>
        function openCinema(src, title = '') {
            const overlay = document.getElementById('cinemaOverlay');
            document.getElementById('cinemaImg').src = src;
            document.getElementById('cinemaTitle').innerText = title;
            overlay.style.display = 'flex';
        }
        function closeCinema(e) {
            if (e.target.id === 'cinemaOverlay' || e.target.id === 'cinemaImg') {
                document.getElementById('cinemaOverlay').style.display = 'none';
            } else if (e.target.id === 'cinemaOverlay') {
                document.getElementById('cinemaOverlay').style.display = 'none';
            } else {
                document.getElementById('cinemaOverlay').style.display = 'none';
            }
        }
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') document.getElementById('cinemaOverlay').style.display = 'none'; });

        async function setActive(id) {
            try {
                const res = await fetch('banner_toggle.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'set_active', id: id })
                });
                const j = await res.json();
                if (!j.ok) { alert('Failed'); return; }

                const tr = document.querySelector(`tr[data-id="${id}"]`);
                if (!tr) return;
                const checkbox = tr.querySelector('input[type="checkbox"][name="set_active"]');
                if (checkbox) checkbox.checked = (j.is_active == 1);

                const existingTag = tr.querySelector('.activeTag');
                if (existingTag) existingTag.remove();

                if (j.is_active == 1) {
                    const td = tr.children[4];
                    const span = document.createElement('span');
                    span.className = 'activeTag';
                    span.innerText = 'ACTIVE';
                    td.insertBefore(span, td.firstChild);
                    if (j.active_order !== undefined && j.active_order !== null) {
                        let small = td.querySelector('small');
                        if (!small) {
                            small = document.createElement('small');
                            small.style.marginLeft = '6px';
                            small.style.color = '#666';
                            td.appendChild(small);
                        }
                        small.innerText = '(#' + j.active_order + ')';
                    }
                } else {
                    const small = tr.children[4].querySelector('small');
                    if (small) small.remove();
                }
            } catch (e) { console.error(e); alert('Error'); }
        }

        async function toggleVisible(id, btn) {
            try {
                const res = await fetch('banner_toggle.php', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'toggle_visible', id: id })
                });
                const j = await res.json();
                if (j.ok) btn.innerText = j.is_visible ? 'Visible' : 'Hidden';
                else alert('Toggle failed');
            } catch (err) { console.error(err); alert('Error'); }
        }

        function confirmDelete(id, btn) {
            if (!confirm('Xác nhận xóa banner này?')) return;
            btn.disabled = true;
            fetch('banner_delete.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id: id }) })
                .then(r => r.json()).then(j => {
                    if (j.ok) {
                        const tr = document.querySelector(`tr[data-id="${id}"]`); if (tr) tr.remove();
                    } else { alert('Delete failed: ' + (j.error || '')); btn.disabled = false; }
                }).catch(e => { console.error(e); alert('Error'); btn.disabled = false; });
        }
    </script>

</body>

</html>
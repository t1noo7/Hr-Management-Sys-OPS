<?php
require_once('../process/dbh.php');

$countSql = "SELECT COUNT(*) as cnt FROM banners WHERE 1=1";
$cRes = mysqli_query($conn, $countSql);
$cRow = mysqli_fetch_assoc($cRes);
$total = intval($cRow['cnt']);
$res = mysqli_query($conn, "SELECT id, banner_title, image_url, is_visible, is_active, COALESCE(active_order, 0) AS active_order FROM banners ORDER BY is_active DESC, active_order ASC, id DESC");
$all = [];
while ($r = mysqli_fetch_assoc($res))
    $all[] = $r;
?>
<!doctype html>
<html>

<head>
    <title>Salary Table | HRMS</title>
    <link rel="stylesheet" type="text/css" href="../styleview.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <!-- font-awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <meta charset="utf-8">
    <title>Banner Reorder — Admin</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 20px;
            background: #f6f8fa;
        }

        h1 {
            margin: 0 0 14px 0
        }

        .wrap {
            display: flex;
            gap: 22px;
            align-items: flex-start;
        }

        .panel {
            background: white;
            border-radius: 10px;
            padding: 14px;
            box-shadow: 0 6px 20px rgba(10, 20, 30, 0.06);
            width: 48%;
        }

        .panel.small {
            width: 28%;
        }

        .hint {
            color: #666;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .list {
            min-height: 150px;
            padding: 8px;
            border-radius: 8px;
            border: 1px dashed #e0e6ee;
            background: #fff;
        }

        .item {
            display: flex;
            gap: 12px;
            align-items: center;
            padding: 8px;
            border-radius: 8px;
            margin-bottom: 8px;
            background: #fff;
            border: 1px solid #eee;
            cursor: grab;
            transition: transform .12s, box-shadow .12s;
        }

        .item.dragging {
            opacity: 0.6;
            transform: scale(.995);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        }

        .item img {
            width: 110px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }

        .meta {
            flex: 1;
        }

        .badge {
            padding: 6px 8px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .badge.active {
            background: #00c27c;
            color: white;
        }

        .badge.normal {
            background: #d1d7e0;
            color: #10203a;
        }

        .order-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: #f0f3f7;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.9rem;
            color: #1a2d5c;
        }

        .controls {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn {
            padding: 8px 12px;
            border-radius: 8px;
            border: 0;
            background: #007bff;
            color: #fff;
            cursor: pointer;
            font-weight: 600;
        }

        .btn.ghost {
            background: #fff;
            border: 1px solid #cbd6ea;
            color: #1a2d5c;
        }

        .btn.save {
            background: linear-gradient(135deg, #00d98d, #00a86b);
        }

        .preview {
            margin-top: 12px;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e6eef8;
            background: #000
        }

        .preview .slide {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: none;
        }

        .preview .slide.show {
            display: block;
        }

        .group-label {
            font-size: 0.95rem;
            margin-bottom: 8px;
            font-weight: 700
        }

        .highlight-active {
            box-shadow: 0 6px 18px rgba(0, 200, 120, 0.08);
            border: 1px solid rgba(0, 200, 120, 0.12)
        }

        .row {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .info {
            color: #415466;
            font-size: 0.92rem;
            margin-bottom: 10px
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <h1>HRMS</h1>
            <ul id="navli">
                <li><a class="homeblack" href="../aloginwel.php">HOME</a></li>

                <li><a class="homeblack" href="addemp.php">Add Employee</a></li>
                <li><a class="homeblack" href="viewemp.php">View Employee</a></li>
                <li><a class="homeblack" href="assign.php">Assign Project</a></li>
                <li><a class="homeblack" href="assignproject.php">Project Status</a></li>
                <li><a class="homeblack" href="admin/dashboard.php">Dashboard</a></li>
                <li><a class="homeblack" href="salaryemp.php">Salary Table</a></li>
                <li><a class="homeblack" href="empleave.php">Employee Leave</a></li>
                <li><a class="homered" href="banner_list.php">Banner</a></li>
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
                <h1 style="margin:0;">Banner Reorder & Preview</h1>
            </header>
            &nbsp;<div style="font-size:0.9em;color:#666;">Total: <?= $total ?> banner(s)</div>
        </div>

        <div class="wrap">
            <!-- LEFT: reorder area -->
            <div class="panel">
                <div class="row">
                    <div style="flex:1">
                        <div id="activeList" class="list"></div>

                        <div style="height:12px"></div>

                        <div class="group-label">VISIBLE (normal) — chạy RANDOM</div>
                        <div id="normalList" class="list"></div>
                    </div>
                </div>

                <div style="margin-top:12px;display:flex;gap:8px;">
                    <button id="btnSave" class="btn save">💾 Save Active Order</button>
                    <button id="btnPreview" class="btn ghost">▶ Preview Slideshow</button>
                    <button id="btnShuffleNormal" class="btn ghost">🔀 Shuffle Normal</button>
                </div>

                <div style="margin-top:10px" class="info">Ghi chú: Save sẽ cập nhật <code>active_order</code> cho các
                    banner
                    active theo thứ tự hiện tại (1 = chạy trước). Nếu admin disable active (uncheck trên banner_list),
                    field
                    sẽ được NULL.</div>
            </div>

            <!-- RIGHT: preview + instructions -->
            <div class="panel small">
                <div class="group-label">Live Preview</div>
                <div class="preview" id="previewBox">
                    <!-- slides will be injected -->
                </div>

                <div style="margin-top:12px;">
                    <div style="font-weight:700;margin-bottom:6px">Drag & Drop, mah Pussy!!!</div>
                    <div class="hint">S.U.P.R.I.S.E - - - - - A.S.S.H.O.L.E</div>
                </div>
            </div>
        </div>

        <script>
            const all = <?php echo json_encode($all, JSON_HEX_TAG | JSON_HEX_AMP); ?>;

            let active = all.filter(x => parseInt(x.is_active) === 1);
            let normal = all.filter(x => parseInt(x.is_active) !== 1 && parseInt(x.is_visible) === 1);

            active.sort((a, b) => (parseInt(a.active_order) || 0) - (parseInt(b.active_order) || 0));

            const activeList = document.getElementById('activeList');
            const normalList = document.getElementById('normalList');
            const previewBox = document.getElementById('previewBox');

            function createItem(b, position = null) {
                const el = document.createElement('div');
                el.className = 'item';
                el.draggable = true;
                el.dataset.id = b.id;

                el.innerHTML = `
    <img src="../${b.image_url}" alt="">
    <div class="meta">
      <div style="display:flex;align-items:center;gap:8px;">
        <div style="font-weight:700">${escapeHtml(b.banner_title || 'Untitled')}</div>
        <div class="badge ${b.is_active == 1 ? 'active' : 'normal'}">${b.is_active == 1 ? 'ACTIVE' : 'NORMAL'}</div>
      </div>
      <div style="margin-top:6px;display:flex;align-items:center;gap:6px;color:#666;font-size:0.9rem;">
        <div>ID: ${b.id}</div>
        ${position !== null ? `<div>• Position: <span class="order-number">${position}</span></div>` : ''}
      </div>
    </div>
  `;
                return el;
            }

            function escapeHtml(s) { return String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c])); }

            function render() {
                activeList.innerHTML = '';
                normalList.innerHTML = '';
                active.forEach((b, idx) => {
                    const it = createItem(b, idx + 1);
                    activeList.appendChild(it);
                });
                normal.forEach(b => {
                    const it = createItem(b);
                    normalList.appendChild(it);
                });
                makeDraggable(activeList);
                makeDraggable(normalList, false);
                buildPreview();
            }

            function makeDraggable(container, allowReorder = true) {
                let dragEl = null;
                container.querySelectorAll('.item').forEach(item => {
                    item.addEventListener('dragstart', e => {
                        dragEl = item;
                        item.classList.add('dragging');
                        e.dataTransfer.effectAllowed = 'move';
                    });
                    item.addEventListener('dragend', e => {
                        item.classList.remove('dragging');
                        dragEl = null;
                        // Update position numbers after drag
                        if (allowReorder && container === activeList) {
                            updatePositionNumbers();
                        }
                    });
                });

                container.addEventListener('dragover', e => {
                    e.preventDefault();
                    const after = getDragAfterElement(container, e.clientY);
                    if (!dragEl) return;
                    if (after == null) container.appendChild(dragEl);
                    else container.insertBefore(dragEl, after);
                });
            }

            function updatePositionNumbers() {
                const items = [...activeList.querySelectorAll('.item')];
                items.forEach((item, idx) => {
                    const posSpan = item.querySelector('.order-number');
                    if (posSpan) {
                        posSpan.textContent = idx + 1;
                    }
                });
            }

            function getDragAfterElement(container, y) {
                const draggableElements = [...container.querySelectorAll('.item:not(.dragging)')];
                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = y - box.top - box.height / 2;
                    if (offset < 0 && offset > closest.offset) {
                        return { offset: offset, element: child };
                    } else {
                        return closest;
                    }
                }, { offset: Number.NEGATIVE_INFINITY }).element;
            }

            let previewIndex = 0, previewTimer = null;
            function buildPreview() {
                previewBox.innerHTML = '';
                const slides = [];
                active.forEach(b => slides.push(b));
                const normalsShuffled = [...normal].sort(() => Math.random() - 0.5);
                normalsShuffled.forEach(b => slides.push(b));

                slides.forEach((s, idx) => {
                    const img = document.createElement('img');
                    img.className = 'slide' + (idx === 0 ? ' show' : '');
                    img.src = "../" + s.image_url;
                    img.alt = s.banner_title || 'banner';
                    previewBox.appendChild(img);
                });

                previewIndex = 0;
            }

            function startPreview() {
                const slides = previewBox.querySelectorAll('.slide');
                if (!slides.length) return;
                stopPreview();
                previewTimer = setInterval(() => {
                    slides[previewIndex].classList.remove('show');
                    previewIndex = (previewIndex + 1) % slides.length;
                    slides[previewIndex].classList.add('show');
                }, 2500);
            }

            function stopPreview() {
                if (previewTimer) { clearInterval(previewTimer); previewTimer = null; }
            }

            document.getElementById('btnSave').addEventListener('click', async () => {
                const items = [...activeList.querySelectorAll('.item')];
                if (items.length === 0) {
                    alert('No active banners to save');
                    return;
                }

                const domOrder = items.map((it, idx) => ({
                    id: it.dataset.id,
                    position: idx + 1
                }));
                console.log('DOM order (visual):', domOrder);

                const payload = [];
                const newActiveOrder = [];

                items.forEach((it, idx) => {
                    const id = parseInt(it.dataset.id);
                    const orderValue = idx + 1;

                    payload.push({ id, order: orderValue });

                    const bannerObj = active.find(a => parseInt(a.id) === id);
                    if (bannerObj) {
                        newActiveOrder.push({
                            ...bannerObj,
                            active_order: orderValue
                        });
                    }
                });

                console.log('Payload to send:', payload);

                try {
                    const res = await fetch('banner_reorder_save.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ order: payload })
                    });
                    const j = await res.json();

                    console.log('Server response:', j);

                    if (j.debug) {
                        console.log('Debug info:', j.debug);
                    }

                    if (j.ok) {
                        active = newActiveOrder;
                        active.sort((a, b) => {
                            const orderA = parseInt(a.active_order) || 0;
                            const orderB = parseInt(b.active_order) || 0;
                            return orderA - orderB;
                        });

                        console.log('Active array after update:', active);

                        render();
                        alert('Saved order ✔');
                    } else {
                        alert('Save failed: ' + (j.error || 'unknown'));
                    }
                } catch (err) {
                    console.error('Error:', err);
                    alert('Error saving order');
                }
            });

            document.getElementById('btnPreview').addEventListener('click', () => {
                stopPreview();
                startPreview();
            });
            document.getElementById('btnShuffleNormal').addEventListener('click', () => {
                normal = normal.sort(() => Math.random() - 0.5);
                render();
            });

            render();
        </script>
</body>

</html>
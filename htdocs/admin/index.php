<?php
session_start();
if (!isset($_SESSION["username"])) {
    $current_path = $_SERVER['REQUEST_URI'];
    header("Location: ../login/?b=" . urlencode($current_path));
    exit;
}
if ($_SESSION["permission_level"] <= 3) {
    echo "<script>
                        window.history.back();
                        console.log('Keine Berechtigung.');
                      </script>";
    exit;
}
define('INCLUDE_GUARD', true);
include "../mysql.php";

/* ---------- ZEITRAUM FILTER LOGIK ---------- */
$range = isset($_GET['range']) ? $_GET['range'] : 'all';

// Standardmäßig leere Bedingungen (für "Alles")
$condCreated = "";
$condPaid = "";
$condCompleted = "";
$condSent = "";
$condTime = "";

// Wenn ein numerischer Zeitraum gewählt wurde, filtern wir die SQLs dynamisch
if (in_array($range, ['7', '30', '365'])) {
    $days = intval($range);
    $condCreated   = " AND project_created_date >= DATE_SUB(CURDATE(), INTERVAL $days DAY) ";
    $condPaid      = " AND invoice_paid_date >= DATE_SUB(CURDATE(), INTERVAL $days DAY) ";
    $condCompleted = " AND completed_date >= DATE_SUB(CURDATE(), INTERVAL $days DAY) ";
    $condSent      = " AND invoice_sent_date >= DATE_SUB(CURDATE(), INTERVAL $days DAY) ";
    $condTime      = " WHERE t.start_time >= DATE_SUB(CURDATE(), INTERVAL $days DAY) ";
}

/* ---------- HELFER ---------- */
function kv($stmt,$k,$v){
    $a=[];
    if ($stmt) {
        while($r=$stmt->fetch(PDO::FETCH_ASSOC)){
            $a[$r[$k]]=(float)$r[$v];
        }
    }
    return $a;
}

/* ---------- KPIs ---------- */
$avgPayDays = $mysql->query("
    SELECT AVG(DATEDIFF(invoice_paid_date, completed_date))
    FROM project
    WHERE invoice_paid_date IS NOT NULL
    $condPaid
")->fetchColumn();

$totalPaid = $mysql->query("
    SELECT SUM(o.order_amount)
    FROM project p
    JOIN `order` o ON o.order_project_id=p.project_id
    WHERE p.project_status='paid'
    $condPaid
")->fetchColumn();

$openProjects = $mysql->query("
    SELECT COUNT(*) FROM project WHERE project_status!='archived' $condCreated
")->fetchColumn();

$openInvoices = $mysql->query("
    SELECT COUNT(*) FROM project
    WHERE project_status IN ('completed','invoice_sent')
    $condCreated
")->fetchColumn();

/* ---------- DIAGRAMME ---------- */

// 1 Status aktuell
$statusNow = kv($mysql->query("
    SELECT project_status,COUNT(*) c 
    FROM project 
    WHERE project_status!='archived'
    $condCreated
    GROUP BY project_status
"),'project_status','c');

// 2 Status pro Tag
function perDay($mysql, $field, $rangeCond) {
    return kv($mysql->query("
        SELECT DATE($field) d,COUNT(*) c
        FROM project
        WHERE $field IS NOT NULL
        AND project_status!='archived'
        $rangeCond
        GROUP BY d
        ORDER BY d
    "),'d','c');
}
$completedDay = perDay($mysql, 'completed_date', $condCompleted);
$billSend     = perDay($mysql, 'invoice_sent_date', $condSent);
$paidDay      = perDay($mysql, 'invoice_paid_date', $condPaid);

// 3 Aktive Status
$statusActive = kv($mysql->query("
    SELECT project_status,COUNT(*) c
    FROM project
    WHERE project_status!='archived'
    $condCreated
    GROUP BY project_status
"),'project_status','c');

// 4 Projekte erstellt
$createdDay = kv($mysql->query("
    SELECT DATE(project_created_date) d,COUNT(*) c
    FROM project
    WHERE project_status!='archived'
    $condCreated
    GROUP BY d 
    ORDER BY d
"),'d','c');

// 5 Projektwert
$projectValue = kv($mysql->query("
    SELECT p.project_name,SUM(o.order_amount) v
    FROM project p
    JOIN `order` o ON o.order_project_id=p.project_id
    WHERE p.project_status!='archived'
    $condCreated
    GROUP BY p.project_id
"),'project_name','v');

// 6 Projektzeit
$projectTime = kv($mysql->query("
    SELECT p.project_name,SUM(t.duration)/60 h
    FROM project p
    JOIN time t ON t.project_id=p.project_id
    WHERE p.project_status!='archived'
    $condCreated
    GROUP BY p.project_id
"),'project_name','h');

// 7 Geld pro Tag
$paidMoneyDay = kv($mysql->query("
    SELECT DATE(p.invoice_paid_date) d,SUM(o.order_amount) v
    FROM project p
    JOIN `order` o ON o.order_project_id=p.project_id
    WHERE p.invoice_paid_date IS NOT NULL
    AND p.project_status!='archived'
    $condPaid
    GROUP BY d ORDER BY d
"),'d','v');

// 8 Geldstatus
$moneyStatus = kv($mysql->query("
    SELECT
        CASE WHEN project_status='paid'
        THEN 'Bezahlt' ELSE 'Offen' END s,
        SUM(o.order_amount) v
    FROM project p
    JOIN `order` o ON o.order_project_id=p.project_id
    WHERE p.project_status!='archived'
    $condCreated
    GROUP BY s
"),'s','v');

// 9 Zeit pro User
$timeUser = kv($mysql->query("
    SELECT u.user_name,SUM(t.duration)/60 h
    FROM time t
    JOIN user u ON u.user_id=t.user_id
    $condTime
    GROUP BY u.user_name
"),'user_name','h');
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/stats.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<h1>Admin Dashboard</h1>
<br>
<div class="admin-links">
    <a class="link" href="../backup">Backup</a>
    <a class="link" href="../log">Log</a>
    <a class="link" href="../settings">Einstellungen</a>
    <a class="link" href="../users">Nutzer</a>
</div>
<a class="back-to-home-link" href="../">Zurück zur Hauptseite</a>
<br>
<br>
<div class="stats-grid">
    <div class="kpi">Ø Zahlungsdauer<br><b><?=round($avgPayDays,1)?> Tage</b></div>
    <div class="kpi">Gesamt bezahlt<br><b><?=round($totalPaid,2)?> €</b></div>
    <div class="kpi">Aktive Projekte<br><b><?=$openProjects?></b></div>
    <div class="kpi">Offene Rechnungen<br><b><?=$openInvoices?></b></div>
</div>

<div style="margin-bottom:15px">
<b>Zeitraum:</b>
<select onchange="applyRange(this.value)">
    <option value="all" <?=($range=='all'?'selected':'')?>>Alles</option>
    <option value="7" <?=($range=='7'?'selected':'')?>>Letzte 7 Tage</option>
    <option value="30" <?=($range=='30'?'selected':'')?>>Letzte 30 Tage</option>
    <option value="365" <?=($range=='365'?'selected':'')?>>Letztes Jahr</option>
</select>
</div>

<div class="charts">
    <div class="box pie">
        <div class="chart-title">Projektstatus aktuell</div>
        <div class="chart-wrap"><canvas id="c1"></canvas></div>
    </div>
    <div class="box">
        <div class="chart-title">Projektstatus im Zeitverlauf</div>
        <div class="chart-wrap"><canvas id="c2"></canvas></div>
    </div>
    <div class="box pie">
        <div class="chart-title">Aktive Projekte (Status)</div>
        <div class="chart-wrap"><canvas id="c3"></canvas></div>
    </div>
    <div class="box">
        <div class="chart-title">Projekt-Erstellungen pro Tag</div>
        <div class="chart-wrap"><canvas id="c4"></canvas></div>
    </div>
    <div class="box pie">
        <div class="chart-title">Projektwert</div>
        <div class="chart-wrap"><canvas id="c5"></canvas></div>
    </div>
    <div class="box pie">
        <div class="chart-title">Zeitaufwand pro Projekt</div>
        <div class="chart-wrap"><canvas id="c6"></canvas></div>
    </div>
    <div class="box">
        <div class="chart-title">Bezahlter Umsatz pro Tag</div>
        <div class="chart-wrap"><canvas id="c7"></canvas></div>
    </div>
    <div class="box pie">
        <div class="chart-title">Umsatzstatus (offen / bezahlt)</div>
        <div class="chart-wrap"><canvas id="c8"></canvas></div>
    </div>
    <div class="box">
        <div class="chart-title">Zeitaufwand pro Nutzer</div>
        <div class="chart-wrap"><canvas id="c9"></canvas></div>
    </div>
</div>

<script>
// JavaScript-Funktion für das dynamische Nachladen bei Filteränderung
function applyRange(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('range', value);
    window.location.href = url.href;
}

const colors = [
    'rgba(54, 162, 235, 0.8)', 'rgba(255, 99, 132, 0.8)', 'rgba(75, 192, 192, 0.8)',
    'rgba(255, 206, 86, 0.8)', 'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)', 'rgba(46, 204, 113, 0.8)'
];
const borderColors = colors.map(c => c.replace('0.8', '1'));

const donut = (id, labels, data) => {
    const ctx = document.getElementById(id);
    if (!ctx || !data || data.length === 0) return;
    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors.slice(0, data.length),
                borderColor: borderColors.slice(0, data.length),
                borderWidth: 1
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom' } } }
    });
};

const line = (id, l, d) => {
    const ctx = document.getElementById(id);
    if (!ctx || !l || l.length === 0) return;
    d.forEach((dataset, index) => {
        dataset.borderColor = borderColors[index % borderColors.length];
        dataset.backgroundColor = colors[index % colors.length];
        dataset.tension = 0.2;
    });
    return new Chart(ctx, { type: 'line', data: { labels: l, datasets: d }, options: { responsive: true, maintainAspectRatio: false } });
};

const bar = (id, l, d) => {
    const ctx = document.getElementById(id);
    if (!ctx || !l || l.length === 0) return;
    d.forEach((dataset, index) => {
        dataset.backgroundColor = colors[index % colors.length];
        dataset.borderColor = borderColors[index % borderColors.length];
        dataset.borderWidth = 1;
    });
    return new Chart(ctx, { type: 'bar', data: { labels: l, datasets: d }, options: { responsive: true, maintainAspectRatio: false } });
};

// JSON_UNESCAPED_UNICODE schützt vor Umlaut-Abbrüchen
donut('c1', <?=json_encode(array_keys($statusNow), JSON_UNESCAPED_UNICODE)?>, <?=json_encode(array_values($statusNow), JSON_UNESCAPED_UNICODE)?>);

line('c2', <?=json_encode(array_keys($completedDay), JSON_UNESCAPED_UNICODE)?>, [
    { label: 'Abgeschlossen', data: <?=json_encode(array_values($completedDay), JSON_UNESCAPED_UNICODE)?> },
    { label: 'Rechnung gesendet', data: <?=json_encode(array_values($billSend), JSON_UNESCAPED_UNICODE)?> },
    { label: 'Bezahlt', data: <?=json_encode(array_values($paidDay), JSON_UNESCAPED_UNICODE)?> }
]);

donut('c3', <?=json_encode(array_keys($statusActive), JSON_UNESCAPED_UNICODE)?>, <?=json_encode(array_values($statusActive), JSON_UNESCAPED_UNICODE)?>);
line('c4', <?=json_encode(array_keys($createdDay), JSON_UNESCAPED_UNICODE)?>, [{ label: 'Projekte erstellt', data: <?=json_encode(array_values($createdDay), JSON_UNESCAPED_UNICODE)?> }]);
donut('c5', <?=json_encode(array_keys($projectValue), JSON_UNESCAPED_UNICODE)?>, <?=json_encode(array_values($projectValue), JSON_UNESCAPED_UNICODE)?>);
donut('c6', <?=json_encode(array_keys($projectTime), JSON_UNESCAPED_UNICODE)?>, <?=json_encode(array_values($projectTime), JSON_UNESCAPED_UNICODE)?>);
line('c7', <?=json_encode(array_keys($paidMoneyDay), JSON_UNESCAPED_UNICODE)?>, [{ label: '€ bezahlt', data: <?=json_encode(array_values($paidMoneyDay), JSON_UNESCAPED_UNICODE)?> }]);
donut('c8', <?=json_encode(array_keys($moneyStatus), JSON_UNESCAPED_UNICODE)?>, <?=json_encode(array_values($moneyStatus), JSON_UNESCAPED_UNICODE)?>);
bar('c9', <?=json_encode(array_keys($timeUser), JSON_UNESCAPED_UNICODE)?>, [{ label: 'Stunden', data: <?=json_encode(array_values($timeUser), JSON_UNESCAPED_UNICODE)?> }]);
</script>

</body>
</html>

<?php
include("../assets/php/connect.php");
session_start();

$viewerId = $_SESSION['userId'];
// $viewerId = 1;

function flash_set($msg){ $_SESSION['flash'] = $msg; }
function flash_pop(){ $m = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $m; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inviteCode = strtoupper(trim($_POST['inviteCode'] ?? ""));

    if (!preg_match('/^[A-Z0-9]{6}$/', $inviteCode)) {
        flash_set("invalid-format");
    } else {
        $stm = $conn->prepare("SELECT circleId,circleName FROM circles WHERE inviteCode=?");
        $stm->bind_param("s",$inviteCode); $stm->execute();
        $circle = $stm->get_result()->fetch_assoc(); $stm->close();

        if (!$circle) {
            flash_set("not-found");
        } else {
            $circleId = $circle['circleId'];
            $circleName = $circle['circleName'];

            $chk = $conn->prepare("SELECT 1 FROM circlemembers WHERE circleId=? AND userId=?");
            $chk->bind_param("ii",$circleId,$viewerId); 
            $chk->execute();
            $isMember = (bool)$chk->get_result()->fetch_row(); 
            $chk->close();

            if ($isMember) {
                flash_set("already-joined");
            } else {
                $ins = $conn->prepare("INSERT INTO circlemembers (circleId,userId,role) VALUES (?,?,'member')");
                $ins->bind_param("ii",$circleId,$viewerId);
                $ins->execute(); 
                $ins->close();

                flash_set(['status' => 'joined-success', 'circleName' => $circleName]); 
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
        }
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

$flash = flash_pop();
$alreadyJoined = ($flash === "already-joined");
$joinedSuccess = is_array($flash) && ($flash['status'] ?? '') === 'joined-success';
$joinedCircleName = $flash['circleName'] ?? '';
$invalidFormat = ($flash === "invalid-format");
$notFound = ($flash === "not-found");
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Join Circle | TODARescue</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../assets/shared/header.php'; ?>
<?php include '../assets/shared/navbarPassenger.php'; ?>

<div class="container-fluid py-5 mt-5 d-flex justify-content-center">
  <div class="flex-grow-1 px-3 pt-5" style="max-width:420px">
    <div class="card border-0 mb-4 rounded-5" style="background:#D9D9D9">
      <div class="card-body text-center py-5">
        <div class="card-text mb-3 fs-3 fw-bold">Enter the Invite Code</div>

        <form id="codeForm" method="POST" class="d-flex flex-column align-items-center gap-3">
          <div class="d-flex justify-content-center align-items-center gap-2">
              <?php for($i=0;$i<6;$i++): ?>
                <input type="text" maxlength="1"
                       class="code-input form-control text-center fw-bold fs-6 py-3 border-0 rounded-4"
                       style="width:45px;height:55px;background:#fff;">
              <?php endfor; ?>
          </div>
          <input type="hidden" name="inviteCode" id="inviteCode">
          <p class="text-muted small mb-2">Get the code from the person<br>setting up your circle.</p>
          <button type="submit" class="btn custom-hover text-black px-4 py-2 rounded-pill fw-semibold">
              JOIN
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- MODAL SUCCESSFULLY JOINED -->
<?php if($joinedSuccess): ?>
<div id="joinedSuccessModal"
     class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center z-1"
     style="background:rgba(0,0,0,0.3);">
  <div class="bg-white p-4 rounded-5 shadow text-center" style="width:85%;max-width:320px;">
    <h5 class="fw-bold mb-3">Joined Successfully</h5>
    <p class="mb-4" style="font-size:.95rem;">
      You’ve successfully joined the circle <strong><?= htmlspecialchars($joinedCircleName) ?></strong>!
    </p>
    <button class="btn rounded-pill px-4 text-white" style="background:#1cc8c8;font-weight:600;" onclick="closeJoinedSuccessModal()">OK</button>
  </div>
</div>
<?php endif; ?>


<!-- MODAL: Already Joined -->
<?php if($alreadyJoined): ?>
<div id="alreadyJoinedModal"
     class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center z-1"
     style="background:rgba(0,0,0,0.3);">
  <div class="bg-white p-4 rounded-5 shadow text-center" style="width:85%;max-width:320px;">
    <h5 class="fw-bold mb-3">Already Joined</h5>
    <p class="mb-4" style="font-size:.95rem;">You’re already a member of this circle.</p>
    <button class="btn rounded-pill px-4 text-white" style="background:#1cc8c8;font-weight:600;" onclick="closeJoinedModal()">OK</button>
  </div>
</div>
<?php endif; ?>

<!-- MODAL INVALID CODE -->
<?php if ($flash === "invalid-format" || $flash === "not-found"): ?>
<div id="invalidCodeModal"
     class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center z-1"
     style="background:rgba(0,0,0,0.3);">
  <div class="bg-white p-4 rounded-5 shadow text-center" style="width:85%;max-width:320px;">
    <h5 class="fw-bold mb-3">Invalid Invite Code</h5>
    <p class="mb-4" style="font-size:.95rem;">
      <?= $flash === "Invalid code format." ? "Please enter a valid 6-character code." : "No circle found for this invite code." ?>
    </p>
    <button class="btn rounded-pill px-4 text-white" style="background:#1cc8c8;font-weight:600;" onclick="closeInvalidModal()">OK</button>
  </div>
</div>
<?php endif; ?>

<script>
const inputs=[...document.querySelectorAll('.code-input')];
  inputs.forEach((inp,idx)=>{
  inp.addEventListener('input',e=>{
      inp.value = inp.value.toUpperCase().replace(/[^A-Z0-9]/,'');
      if(inp.value && idx<5) inputs[idx+1].focus();
      document.getElementById('inviteCode').value = inputs.map(i=>i.value).join('');
  });
  inp.addEventListener('keydown',e=>{
      if(e.key==='Backspace' && !inp.value && idx>0) inputs[idx-1].focus();
  });
});''
  function closeJoinedModal() {
      const modal = document.getElementById('alreadyJoinedModal');
      if(modal) modal.remove();
  }
  function closeInvalidModal() {
      const modal = document.getElementById('invalidCodeModal');
      if (modal) modal.remove();
  }
  function closeJoinedSuccessModal() {
      const modal = document.getElementById('joinedSuccessModal');
      if(modal) modal.remove();
  }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

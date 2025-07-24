<?php
include("../assets/php/connect.php");  
session_start();

if (!isset($_SESSION['userId'])) {
    header("Location: ../index.php");
    exit;
}
$creatorId = $_SESSION['userId']; 
// $creatorId = 2;

function flashSet(array $data){ $_SESSION['flash'] = $data; }
function flashGet(){
    $data = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $data;
}

// Generate a unique code with specified length
function makeCode(int $len = 6): string {
    global $conn;
    $set = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
    $isUnique = false;
    $maxAttempts = 10;
    $attempts = 0;
    
    while (!$isUnique && $attempts < $maxAttempts) {
        $out = '';
        for ($i=0; $i<$len; $i++) {
            $out .= $set[random_int(0, strlen($set)-1)];
        }
        
        // Check if code already exists
        $chk = $conn->prepare("SELECT COUNT(*) FROM circles WHERE inviteCode=?");
        $chk->bind_param("s", $out);
        $chk->execute();
        $count = $chk->get_result()->fetch_row()[0];
        $chk->close();
        
        if ($count == 0) {
            $isUnique = true;
        }
        
        $attempts++;
    }
    
    return $out;
}

$nameError = "";
$flash     = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $circleName = trim($_POST['circleName'] ?? "");

    if ($circleName === "" || mb_strlen($circleName) > 40) {
        $nameError = "Circle name is required (max 50 characters).";
    } else {
        $chkName = $conn->prepare("SELECT 1 FROM circles WHERE circleName = ? AND userId = ?");
        $chkName->bind_param("si", $circleName, $creatorId);
        $chkName->execute();
        $nameExists = $chkName->get_result()->fetch_row();
        $chkName->close();


        if (!$nameExists) {
            do {
                $inviteCode = makeCode();
                $chk = $conn->prepare("SELECT 1 FROM circles WHERE inviteCode=?");
                $chk->bind_param("s", $inviteCode); $chk->execute();
                $dup = $chk->get_result()->fetch_row(); $chk->close();
            } while ($dup);
            $ins = $conn->prepare(
                "INSERT INTO circles (circleName, inviteCode, userId) VALUES (?,?,?)"
            );
            $ins->bind_param("ssi", $circleName, $inviteCode, $creatorId);
            $ins->execute(); $circleId = $ins->insert_id; $ins->close();
            $mem = $conn->prepare(
                "INSERT INTO circlemembers (circleId, userId, role) VALUES (?, ?, 'owner')"
            );
            if (!$mem) {
                die("Prepare failed: " . $conn->error);
            }
            $mem->bind_param("ii", $circleId, $creatorId);
            if (!$mem->execute()) {
                die("Execute failed: " . $mem->error);
            }
            $mem->close();

            flashSet(['name' => $circleName, 'code' => $inviteCode]);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $showNameExistsModal = true;
        }
    }
}

$flash = flashGet();
if (!is_array($flash)) $flash = null;
?>


<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Create Circle | TODARescue</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../assets/shared/header.php'; ?>
<?php include '../assets/shared/navbarDriver.php'; ?>

<div class="container-fluid py-5 mt-5 d-flex flex-column">
  <div class="flex-grow-1 px-3 pt-4">
    <h3 class="fw-bold mb-3">Name Your Circle</h3>

    <form method="POST" novalidate>
      <input type="text" name="circleName" id="circleName"
             class="form-control border-0 rounded-pill py-3 px-4"
             placeholder="Enter the name for your new circle"
             maxlength="50" required style="background:#d9d9d9">
      <div class="text-center mt-4">
        <button id="submitBtn" class="btn custom-hover px-4 py-2 rounded-pill fw-bold">
          SUBMIT
        </button>
      </div>
    </form>
  </div>
</div>

<!--  Invite‑Code Modal  -->
<div id="inviteModalBackdrop"
     class="position-fixed top-0 start-0 w-100 h-100 d-flex <?= $flash ? '' : 'd-none' ?>
            justify-content-center align-items-center z-1"
     style="background:rgba(255,255,255,0.4);">
  <div class="bg-white p-4 rounded-5 shadow text-center" style="width:85%;max-width:320px;">
      <h5 class="fw-bold mb-2"><?= htmlspecialchars($flash['name']) ?> Created!</h5>
      <?php if($flash): ?>
      <?php endif; ?>
      <p style="font-size:.95rem;">Share this invite code:</p>

      <div class="border rounded-pill px-4 py-2 mb-3 d-flex justify-content-center"
           style="font-size:1.2rem;font-weight:700;letter-spacing:1px;">
          <span id="inviteCodeText"><?= $flash ? htmlspecialchars($flash['code']) : '' ?></span>
      </div>

      <div class="d-flex justify-content-center gap-3">
          <button class="btn rounded-pill px-4 text-white"
                  style="background:#1cc8c8;font-weight:600;"
                  onclick="copyInviteCode()">Copy</button>
          <button class="btn rounded-pill px-4"
                  style="background:#dcdcdc;font-weight:600;"
                  onclick="closeInviteModal()">Close</button>
      </div>
  </div>
</div>

<!-- Circle Name Exists Modal -->
<div id="nameExistsModal"
     class="position-fixed top-0 start-0 w-100 h-100 d-flex <?= !empty($showNameExistsModal) ? '' : 'd-none' ?>
            justify-content-center align-items-center z-1"
     style="background:rgba(255,255,255,0.4);">
  <div class="bg-white p-4 rounded-5 shadow text-center" style="width:85%;max-width:320px;">
      <h5 class="fw-bold mb-2 text-danger">Circle Name Exists</h5>
      <p style="font-size:.95rem;">The name <strong><?= htmlspecialchars($circleName ?? '') ?></strong> is already taken.</p>
      <p style="font-size:.9rem;">Please choose a different name for your circle.</p>

      <div class="d-flex justify-content-center">
          <button class="btn rounded-pill px-4"
                  style="background:#dcdcdc;font-weight:600;"
                  onclick="closeNameExistsModal()">Close</button>
      </div>
  </div>
</div>

<!-- Copied Modal -->
<div id="copiedModal"
     class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center d-none"
     style="background:rgba(0,0,0,0.3); z-index:1055;">
  <div class="bg-white p-3 rounded-4 shadow text-center" style="width:80%; max-width:280px;">
    <p class="mb-0 fw-semibold" style="font-size:0.95rem;">Invite code copied to clipboard!</p>
  </div>
</div>


<script>
const nameInput=document.getElementById('circleName');
const submitBtn=document.getElementById('submitBtn');
nameInput.addEventListener('input',()=>submitBtn.disabled=nameInput.value.trim().length===0||nameInput.value.trim().length>50);

function closeInviteModal(){
   document.getElementById('inviteModalBackdrop').classList.add('d-none');
    window.location.href = 'groupPage.php';
}

function copyInviteCode() {
    const codeText = document.getElementById('inviteCodeText').textContent;
    navigator.clipboard.writeText(codeText).then(() => {
        const modal = document.getElementById('copiedModal');
        modal.classList.remove('d-none');
        setTimeout(() => {
            modal.classList.add('d-none');
        }, 2000);
    });
}


function closeNameExistsModal(){
   document.getElementById('nameExistsModal').classList.add('d-none');
   document.getElementById('circleName').focus();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

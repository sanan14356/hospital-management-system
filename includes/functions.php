<?php
function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect($path)
{
    header("Location: $path");
    exit;
}

function isPost()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function post($key, $default = '')
{
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

function get($key, $default = '')
{
    return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
}

function setFlash($type, $message)
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlash()
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    return null;
}

function renderFlash()
{
    $flash = getFlash();
    if (!$flash) {
        return;
    }

    $typeClass = 'alert-info';
    if ($flash['type'] === 'success') {
        $typeClass = 'alert-success';
    } elseif ($flash['type'] === 'error') {
        $typeClass = 'alert-error';
    }

    echo '<div class="alert ' . $typeClass . '">' . e($flash['message']) . '</div>';
}

function validateName($name)
{
    return (bool)preg_match("/^[a-zA-Z\s'.-]+$/", $name);
}

function validateTextNoNumbers($text)
{
    if (trim($text) === '') {
        return false;
    }

    return (bool)preg_match("/^[a-zA-Z\s&()'.,\/-]+$/", $text);
}

function validatePhone($phone)
{
    return (bool)preg_match('/^\+?[0-9]{7,15}$/', $phone);
}

function validateEmail($email)
{
    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
}

function bindParams($stmt, $types, $params)
{
    if ($types === '' || empty($params)) {
        return;
    }

    $bindNames = [];
    $bindNames[] = $types;

    foreach ($params as $key => $value) {
        $bindNames[] = &$params[$key];
    }

    call_user_func_array([$stmt, 'bind_param'], $bindNames);
}

function formatDate($date)
{
    if (!$date) {
        return '';
    }

    return date('M d, Y', strtotime($date));
}

function selectedAttr($value, $current)
{
    return $value == $current ? 'selected' : '';
}

function checkedAttr($value, $current)
{
    return $value == $current ? 'checked' : '';
}

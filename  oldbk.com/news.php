<?php

if (isset($_REQUEST['topic'])) {
    header("Location: https://oldbk.com/news/".(int)$_REQUEST['topic'], true, 301);
} else {
    header("Location: https://oldbk.com/news", true, 301);
}
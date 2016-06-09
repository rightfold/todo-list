<?php
function load() {
    $entries = json_decode(file_get_contents(__DIR__ . '/entries.json'));
    usort($entries, function($a, $b) { return $a->done - $b->done; });
    return $entries;
}

function save($entries) {
    file_put_contents(__DIR__ . '/entries.json', json_encode($entries));
}

function add($text) {
    $entries = load();
    $entries[] = (object)['done' => false, 'text' => $text];
    save($entries);
}

function toggle($i) {
    $entries = load();
    $entries[$i]->done = !$entries[$i]->done;
    save($entries);
}

function delete($i) {
    $entries = load();
    array_splice($entries, $i, 1);
    save($entries);
}

if (!file_exists(__DIR__ . '/entries.json')) {
    save([]);
}

if (array_key_exists('add', $_POST)) {
    add($_POST['text']);
}

if (array_key_exists('toggle', $_POST)) {
    foreach (array_keys($_POST['toggle']) as $i) {
        toggle($i);
    }
    header('location: /');
}

if (array_key_exists('delete', $_POST)) {
    foreach (array_keys($_POST['delete']) as $i) {
        delete($i);
    }
    header('location: /');
}
?>

<title>todo list</title>

<form method="POST">
    <input type="text" name="text">
    <button name="add">add</button>

    <ul>
        <?php foreach (load() as $i => $entry): ?>
            <li>
                <button name="toggle[<?= $i ?>]">
                    <?php if ($entry->done): ?>
                        &#9745;
                    <?php else: ?>
                        &#9744;
                    <?php endif ?>
                </button>
                <button name="delete[<?= $i ?>]">
                    delete
                </button>
                <?= htmlentities($entry->text) ?>
            </li>
        <?php endforeach ?>
    </ul>
</form>

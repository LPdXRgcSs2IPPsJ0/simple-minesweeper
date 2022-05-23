<?php

require '../autoload.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Minesweeper</title>
    <link type='text/css' rel='stylesheet' href='css/minesweeper.css'>
    <script src="https://kit.fontawesome.com/8bdde58a6f.js" crossorigin="anonymous"></script>
</head>
<body>

<div id="container">
    <form>
        <fieldset>
            <legend>
                Choose difficulty:
            </legend>
            <input type="radio" id="beginner" name="new_game" value="beginner" checked><label for="beginner">Beginner (10x10, 10 mines)</label>
            <input type="radio" id="intermediate" name="new_game" value="intermediate"><label for="intermediate">Intermediate (16x16, 26 mines)</label>
            <input type="radio" id="expert" name="new_game" value="expert"><label for="expert">Expert (24x24, 24 mines)</label>
            <br>
            <input type="radio" id="custom" name="new_game" value="custom"><label for="custom">Custom</label>
            <br>
            <div id="custom-game-container">
                <label for="height">Height: </label><input type="number" id="height" name="height" value="10" min="4" max="99">
                <label for="width">Width: </label><input type="number" id="width" name="width" value="16" min="4" max="99">
                <label for="number_of_mines">Number of Mines: </label><input type="number" id="number_of_mines" name="number_of_mines" value="16" min="1" max="9999">
            </div>
            <button type="submit">New game</button>
        </fieldset>
    </form>

    <?php

    if (isset($_GET['new_game'])) {
        switch ($_GET['new_game']) {
            case 'beginner':
                $_SESSION['game'] = new Game(10, 10, 10);
                break;
            case 'intermediate':
                $_SESSION['game'] = new Game(16, 16, 26);
                break;
            case 'expert':
                $_SESSION['game'] = new Game(24, 24, 58);
                break;
            case 'custom':
                if (isset($_GET['width']) && isset($_GET['height']) && isset($_GET['number_of_mines'])) {
                    $_SESSION['game'] = new Game($_GET['width'], $_GET['height'], $_GET['number_of_mines']);
                } else {
                    $_SESSION['game'] = new Game(10, 10, 10);
                }
                break;
            default:
                $_SESSION['game'] = new Game(10, 10, 10);
        }
    }

    if (isset($_SESSION['game'])) {
        if (isset($_GET['reveal'])) {
            $coordinates = explode(',', $_GET['reveal']);
            $_SESSION['game']->clickTile($coordinates[0], $coordinates[1]);
        } elseif (isset($_GET['flag'])) {
            $coordinates = explode(',', $_GET['flag']);
            $_SESSION['game']->flagTile($coordinates[0], $coordinates[1]);
        }
    } else {
        $game = new Game(10, 10, 10);
        $_SESSION['game'] = $game;
    }
    ?>

    <div id="game-status-container">
        <?php
        if ($_SESSION['game']->checkIfLost()) {
            echo "You lose! <i class='fa-solid fa-face-frown'></i>";
        } elseif ($_SESSION['game']->getRemainingSafeTiles() == 0) {
            echo "You win! <i class='fa-solid fa-face-smile-beam'></i>";
        }
        ?>
    </div>

    <div id="statistics-container">
        <?php
        echo "<p>Remaning Safe Tiles:" . $_SESSION['game']->getRemainingSafeTiles() . "</p>";
        echo "<p>Number of Mines:" . $_SESSION['game']->getNumberOfMines() . "</p>";
        echo "<p>Flagged Tiles:" . $_SESSION['game']->getNumberOfFlaggedTiles() . "</p>";
        ?>
    </div>

    <?php
    echo($_SESSION['game']->boardToHtml());
    ?>

    <hr>

    <div id="rules-container">
        <h1>How to play:</h1>
        <p>The aim of the game is to click every tile that does not contain a mine. If you click a tile containing a mine, you lose.</p>
        <p>If you click on a tile that does not contain a mine, it will be marked with the number of mines that are in adjacent tiles. Your first click will never be a mine.</p>
        <p>If you right-click on a tile, you can mark it with a flag. This can be used to remember where mines are. Right-clicking the tile again will remove the flag.</p>
        <p>For custom boards, the maximum size is 99x99 tiles. The maximum number of mines is calculated using this formula: (width-1) * (height-1)</p>
    </div>
</div>

<script type='text/javascript' src='js/jquery-3.6.0.slim.min.js'></script>
<script type='text/javascript' src='js/minesweeper.js'></script>
</body>
</html>
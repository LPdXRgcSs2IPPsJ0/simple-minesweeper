<?php

require_once 'Tile.php';

/**
 * Contains the properties of a Minesweeper game.
 *
 * @author Aimée
 */
class Game
{
    /**
     * @var int The width of the game board.
     */
    private int $width;

    /**
     * @var int The height of the game board.
     */
    private int $height;

    /**
     * @var int The number of mines the game board will contain.
     */
    private int $number_of_mines;

    /**
     * @var bool Whether the mines have been added to the board.
     */
    private bool $initialised = false;

    /**
     * @var bool Whether the game has been lost.
     */
    private bool $is_lost = false;

    /**
     * @var array An array of Tiles representing the game board
     */
    private array $board;

    /**
     * @var int The number of tiles that have been revealed.
     */
    private int $number_of_revealed_tiles = 0;

    /**
     * @var int The number of tiles that have been flagged.
     */
    private int $number_of_flagged_tiles = 0;

    /**
     * @param int $width The width of the game board
     * @param int $height The height of the game board
     * @param int $number_of_mines The number of mines that will be placed on the game board
     */
    public function __construct(int $width, int $height, int $number_of_mines)
    {
        $max_number_of_mines = ($width - 1) * ($height - 1);

        $this->width = $width;
        $this->height = $height;
        $this->number_of_mines = min($max_number_of_mines, max($number_of_mines, 1));

        $board = array_fill(0, $height, array());

        foreach ($board as $i => $row) {
            for ($j = 0; $j < $width; $j++) {
                $row[$j] = new Tile();
            }
            $board[$i] = $row;
        }
        $this->board = $board;
    }

    /**
     * Converts the game board into an HTML table.
     *
     * @return string
     */
    public function boardToHtml(): string
    {
        $html = "<table>";

        foreach ($this->board as $y => $column) {
            $html .= "<tr>";

            foreach ($column as $x => $tile) {
                $html .= "<td>";

                if ($tile->isRevealed()) {
                    if ($tile->hasMine()) {
                        $html .= "<i class='fa-solid fa-land-mine-on mine'></i>";
                    } elseif ($tile->getNumberOfAdjacentMines() > 0) {
                        $html .= $tile->getNumberOfAdjacentMines();
                    }
                } elseif ($tile->isFlagged()) {
                    $html .= "<span class='tile flag' data-coordinates='$y,$x'><i class='fa-solid fa-flag'></i></span>";
                } elseif ($this->is_lost) {
                    $html .= "<span class='tile'> </span>";
                } else {
                    $html .= "<a class='tile' href='?reveal=$y,$x'> </a>";
                }

                $html .= "</td>";
            }

            $html = $html . "</tr>";
        }

        $html .= "</table>";
        return $html;
    }

    /**
     * Handles a player clicking on a tile. Reveals the tile and initialises the game if it has not already been done.
     *
     * @param int $y Y co-ordinate of tile within game board array
     * @param int $x X co-ordinate of tile within game board array
     * @return void
     */
    public function clickTile(int $y, int $x): void
    {
        $chosen_tile = $this->board[$y][$x];

        if ($this->initialised) {
            if ($chosen_tile->hasMine()) {
                $this->is_lost = true;
                $this->revealAllMines();
            } elseif ($chosen_tile->getNumberOfAdjacentMines() == 0) {
                $this->revealTile($chosen_tile);
                $this->revealAdjacentNoMinesTiles($y, $x);
            } else {
                $this->revealTile($chosen_tile);
            }
        } else {
            $this->revealTile($chosen_tile);
            $this->placeMines();
            if ($chosen_tile->getNumberOfAdjacentMines() == 0) {
                $this->revealAdjacentNoMinesTiles($y, $x);
            }
        }
    }

    /**
     * Reveals all the tiles adjacent to the given tile. To be used when a revealed tile has no adjacent mines.
     *
     * @param int $y Y co-ordinate of tile within game board array
     * @param int $x X co-ordinate of tile within game board array
     * @return void
     */
    public function revealAdjacentNoMinesTiles(int $y, int $x): void
    {
        for ($i = 1; $i >= -1; $i--) {
            for ($j = 1; $j >= -1; $j--) {
                if ($y + $i >= 0 && $x + $j >= 0 && $y + $i < $this->height && $x + $j < $this->width) {
                    if (!$this->board[$y + $i][$x + $j]->isRevealed()) {
                        $this->clickTile($y + $i, $x + $j);
                    }
                }
            }
        }
    }

    /**
     * Checks whether a tile has been revealea before revealing it if it has not. Increments the number of revealed tiles.
     *
     * @param Tile $chosen_tile The tile to reveal
     * @return void
     */
    public function revealTile(Tile $chosen_tile): void
    {
        if (!$chosen_tile->isRevealed()) {
            $chosen_tile->reveal();
            $this->number_of_revealed_tiles += 1;
        }
    }

    /**
     * Toggles the flagged status of a tile. Increments and decrements the number of flagged tiles as necessary.
     *
     * @param int $y Y co-ordinate of tile within game board array
     * @param int $x X co-ordinate of tile within game board array
     * @return void
     */
    public function flagTile(int $y, int $x): void
    {
        $this->board[$y][$x]->toggleFlag();
        if ($this->board[$y][$x]->isFlagged()) {
            $this->number_of_flagged_tiles += 1;
        } else {
            $this->number_of_flagged_tiles -= 1;
        }
    }

    /**
     * Gets number of mines.
     *
     * @return int
     */
    public function getNumberOfMines(): int
    {
        return $this->number_of_mines;
    }

    /**
     * Reveals all the mines on the game board.
     *
     * @return void
     */
    public function revealAllMines(): void
    {
        foreach ($this->board as $y => $column) {
            foreach ($column as $x => $tile) {
                if ($tile->hasMine() && !$tile->isRevealed()) {
                    $tile->reveal();
                }
            }
        }
    }

    /**
     * Randomly places mines throughout the game board.
     *
     * @return void
     */
    public function placeMines(): void
    {
        $mines_to_place = $this->number_of_mines;
        while ($mines_to_place != 0) {
            $x = rand(0, $this->width - 1);
            $y = rand(0, $this->height - 1);
            $current_tile = $this->board[$y][$x];

            if (!$current_tile->hasMine() && !$current_tile->isRevealed()) {
                $current_tile->addMine();

                for ($i = 1; $i >= -1; $i--) {
                    for ($j = 1; $j >= -1; $j--) {
                        if ($y + $i >= 0 && $x + $j >= 0 && $y + $i < $this->height && $x + $j < $this->width) {
                            $this->board[$y + $i][$x + $j]->increaseNumberOfAdjacentMines(1);
                        }
                    }
                }
                $mines_to_place--;
            }
        }
        $this->initialised = true;
    }

    /**
     * Checks if the game has been lost.
     *
     * @return bool
     */
    public function checkIfLost(): bool
    {
        return $this->is_lost;
    }

    /**
     * Gets the number of tiles that are flagged.
     *
     * @return int
     */
    public function getNumberOfFlaggedTiles(): int
    {
        return $this->number_of_flagged_tiles;
    }

    /**
     * Gets the number of safe tiles (tiles which do not contain mines) that have not been revealed.
     *
     * @return int
     */
    public function getRemainingSafeTiles(): int
    {
        return $this->width * $this->height - $this->number_of_revealed_tiles - $this->number_of_mines;
    }
}
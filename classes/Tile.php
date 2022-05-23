<?php

/**
 * Represents a single square of the Minesweeper game board.
 *
 *  @author Aimée
 */
class Tile
{
    /**
     * @var bool Whether the tile has a mine.
     */
    private bool $has_mine = false;

    /**
     * @var bool Whether the tile is revealed.
     */
    private bool $is_revealed = false;

    /**
     * @var bool Whether the tile has been flagged.
     */
    private bool $is_flagged = false;

    /**
     * @var int A record of the number of mines that are adjacent to the tile.
     */
    private int $number_of_adjacent_mines = 0;

    public function __construct()
    {
    }

    /**
     * Returns whether the tile contains a mine.
     *
     * @return bool
     */
    public function hasMine(): bool
    {
        return $this->has_mine;
    }

    /**
     * Returns whether the tile is revealed.
     *
     * @return bool
     */
    public function isRevealed(): bool
    {
        return $this->is_revealed;
    }

    /**
     * Returns whether the tile is flagged.
     *
     * @return bool
     */
    public function isFlagged(): bool
    {
        return $this->is_flagged;
    }

    /**
     * Returns the stored record of the number of mines that are adjacent to the tile.
     *
     * @return int
     */
    public function getNumberOfAdjacentMines(): int
    {
        return $this->number_of_adjacent_mines;
    }

    /**
     * Changes the revealed status of a tile.
     *
     * @return void
     */
    public function reveal(): void
    {
        $this->is_revealed = true;
    }

    /**
     * Toggles the flagged status of the tile.
     *
     * @return void
     */
    public function toggleFlag(): void
    {
        $this->is_flagged = !$this->is_flagged;
    }

    /**
     * Adds a mine to the tile.
     *
     * @return void
     */
    public function addMine(): void
    {
        $this->has_mine = true;
    }

    /**
     * Changes the record of the number of mines adjacent to a tile.
     *
     * @param int $number The value to increase the number of adjacent mines by
     * @return void
     */
    public function increaseNumberOfAdjacentMines(int $number): void
    {
        $this->number_of_adjacent_mines += $number;
    }
}
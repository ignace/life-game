<?php

/**
 * Life game
 *
 * @copyright Copyright (c) 2013 Shane Auckland
 * @license   http://shaneauckland.co.uk/LICENSE.txt
 * @author    Shane Auckland <shane.auckland@gmail.com>
 */ 

namespace Life;

/**
 * Main game class
 */

class Game
{
    protected $board;

    protected $engine;

    /**
     * Constructor accepts an $options array for quickstart
     * 
     * @param array $options Config options
     *
     * @return void
     */
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->newGame($options);
        }
    }

    /**
     * Start a new session, either with grid dimensions or a seed file,
     * as defined in the passed config object.
     *
     * @param array $options Options array
     *
     * @return \Life\Game
     */
    public function newGame(array $options)
    {
        // test for a seed file first
        if (array_key_exists('file', $options)) {
            $this->getBoard()->createFromFile($options['file']);
            return $this;
        }
        // check for a manually defined array
        if (array_key_exists('grid', $options)) {
            $this->getBoard()->setGrid($options['grid']);
            return $this;
        }
        // check for dimensions
        if (array_key_exists('width', $options) && array_key_exists('height', $options)) {
            $this->getBoard()->createRandom($options['width'], $options['height']);
            return $this;
        }
        // required options not satisfied, fail
        throw new \BadMethodCallException('Supplied options must define either a file or dimensions');
    }

    /**
     * Lazy load and return a board instance
     *
     * @return \Life\Board
     */
    public function getBoard()
    {
        if (is_null($this->board)) {
            $this->board = new Board();
        }
        return $this->board;
    }

    /**
     * Lazy-load and return the engine
     *
     * @return \Life\Engine
     */
    protected function getEngine()
    {
        if (is_null($this->engine)) {
            $this->engine = new Engine();
        }
        return $this->engine;
    }

    /**
     * Trigger the board to update with a new generation
     *
     * @return \Life\Game
     */
    public function takeTurn()
    {
        $this->getEngine()->updateGeneration($this->getBoard());
        return $this;
    }
}


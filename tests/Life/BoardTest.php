<?php

class BoardTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test instance
     */
    public function testBoardInstance()
    {
        $board = new \Life\Board();
        $this->assertInstanceOf('\Life\Board', $board);
    }

    /**
     * Test that the grid starts empty
     *
     * @covers \Life\Board::count
     */
    public function testGridStartsEmpty()
    {
        $board = new \Life\Board();
        $this->assertEquals(0, $board->count());
    }

    /**
     * Test that the expected exception is thrown when trying to create a grid 
     * from a file that doesn't exist.
     *
     * @covers \Life\Board::createFromFile
     */
    public function testCreateWithMissingFileException()
    {
        $board = new \Life\Board();

        $this->setExpectedException(
            '\Life\Exception\BoardException',
            'Failed to create grid from file'
        );

        $board->createFromFile('thisfiledoesnotexist.txt');
    }

    /**
     * Test the expected grid is generated from a valid sample file
     *
     * @covers \Life\Board::createFromFile
     * @covers \Life\Board::getGridFromFile
     * @covers \Life\Board::getValidLine
     * @covers \Life\Board::validateLine
     */
    public function testGridFromValidFile()
    {
        $board = new \Life\Board();
        
        $expected = new \Life\Board();
        $refClass = new ReflectionClass($expected);
        $grid = $refClass->getProperty('grid');
        $grid->setAccessible(true);

        $testGrid = array( 
            array('0', '0', '1', '0', '0', '0', '0', '0'),            
            array('0', '0', '1', '1', '0', '0', '1', '0'),
            array('0', '0', '1', '0', '0', '1', '0', '0'),
            array('0', '1', '1', '0', '0', '1', '1', '0'),
            array('0', '1', '1', '1', '1', '0', '1', '1'),
            array('0', '0', '1', '1', '0', '0', '1', '1'),
            array('0', '0', '1', '0', '1', '0', '1', '0'),
            array('0', '0', '0', '1', '1', '0', '0', '1')
        );

        $grid->setValue($expected, $testGrid);
        
        $board->createFromFile('boards/valid.txt');
        $this->assertEquals($expected, $board);
    }

    /**
     * Test that grid generation fails when the line lengths in the supplied 
     * file do not match
     *
     * @covers \Life\Board::createFromFile
     * @covers \Life\Board::getGridFromFile
     * @covers \Life\Board::getValidLine
     * @covers \Life\Board::validateLine
     */
    public function testGridCreateFailsWithShortLineLength()
    {
        $board = new \Life\Board();

        try {
            $board->createFromFile('boards/short-length.txt');
        } catch (\Life\Exception\BoardException $exception) {
            $this->assertEquals('Failed to create grid from file', $exception->getMessage());
            $previous = $exception->getPrevious();
            $this->assertInstanceOf('\Life\Exception\BoardException', $previous);
            $this->assertEquals(
                'File read failed on line 3 with message "Line length is 7, 8 expected"',
                $previous->getMessage()
            );
            return;
        }

        $this->fail('Expected exception was not thrown');
    }


    /**
     * Test that grid generation fails when the line lengths in the supplied 
     * file do not match
     *
     * @covers \Life\Board::createFromFile
     * @covers \Life\Board::getGridFromFile
     * @covers \Life\Board::getValidLine
     * @covers \Life\Board::validateLine
     */
    public function testGridCreateFailsWithLongLineLength()
    {
        $board = new \Life\Board();

        try {
            $board->createFromFile('boards/long-length.txt');
        } catch (\Life\Exception\BoardException $exception) {
            $this->assertEquals('Failed to create grid from file', $exception->getMessage());
            $previous = $exception->getPrevious();
            $this->assertInstanceOf('\Life\Exception\BoardException', $previous);
            $this->assertEquals(
                'File read failed on line 6 with message "Line length is 9, 8 expected"',
                $previous->getMessage()
            );
            return;
        }

        $this->fail('Expected exception was not thrown');
    }


    /**
     * Test that grid generation fails when an unexpected character is found
     *
     * @covers \Life\Board::createFromFile
     * @covers \Life\Board::getGridFromFile
     * @covers \Life\Board::getValidLine
     * @covers \Life\Board::validateLine
     */
    public function testGridCreateFailsWithUnexpectedCharacter()
    {
        $board = new \Life\Board();

        try {
            $board->createFromFile('boards/bad-character.txt');
        } catch (\Life\Exception\BoardException $exception) {
            $this->assertEquals('Failed to create grid from file', $exception->getMessage());
            $previous = $exception->getPrevious();
            $this->assertInstanceOf('\Life\Exception\BoardException', $previous);
            $this->assertEquals(
                'File read failed on line 4 with message "Encountered unexpected character: a"',
                $previous->getMessage()
            );
            return;
        }

        $this->fail('Expected exception was not thrown');
    }

    /**
     * Test that create random grid fails with a width less than 3
     *
     * @covers \Life\Board::createRandom
     */
    public function testCreateRandomFailsOnSmallWidth()
    {
        $this->setExpectedException(
            '\Life\Exception\BoardException',
            'Width must be an integer of 3 or more, 2 provided'
        );

        $board = new \Life\Board();
        $board->createRandom(2, 3);
    }

    /**
     * Test that create random grid fails with a non-integer width
     *
     * @covers \Life\Board::createRandom
     */
    public function testCreateRandomFailsOnNonIntWidth()
    {
        $this->setExpectedException(
            '\Life\Exception\BoardException',
            'Width must be an integer of 3 or more, a provided'
        );

        $board = new \Life\Board();
        $board->createRandom('a', 3);
    }

    /**
     * Test that create random grid fails with a height less than 3
     *
     * @covers \Life\Board::createRandom
     */
    public function testCreateRandomFailsOnSmallHeight()
    {
        $this->setExpectedException(
            '\Life\Exception\BoardException',
            'Height must be an integer of 3 or more, 2 provided'
        );

        $board = new \Life\Board();
        $board->createRandom(3, 2);
    }

    /**
     * Test that create random grid fails with a non-integer height
     *
     * @covers \Life\Board::createRandom
     */
    public function testCreateRandomFailsOnNonIntHeight()
    {
        $this->setExpectedException(
            '\Life\Exception\BoardException',
            'Height must be an integer of 3 or more, b provided'
        );

        $board = new \Life\Board();
        $board->createRandom(3, 'b');
    }

    /**
     * Test that create random grid creates a grid to the specified dimensions
     *
     * @covers \Life\Board::createRandom
     * @covers \Life\Board::createRandomGridRow
     * @covers \Life\Board::count
     */
    public function testRandomGridDimensions()
    {
        $board = new \Life\Board();
        $board->createRandom(6, 5);

        $this->assertEquals(5, $board->count());

        foreach ($board as $row) {
            $this->assertInternalType('array', $row);
            $this->assertCount(6, $row);
        }
    }

    /**
     * Test that create random grid only returns 1 and 0 in the grid
     *
     * @covers \Life\Board::createRandom
     * @covers \Life\Board::createRandomGridRow
     */
    public function testRandomGridContents()
    {
        $board = new \Life\Board();
        $board->createRandom(3, 3);

        foreach ($board as $row) {
            $filtered = array_filter($row, function ($element) {
                return ($element !== 0 && $element !== 1);
            });
            $this->assertEmpty($filtered);
        }
    }
}

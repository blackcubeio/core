<?php
/**
 * MatrixHelper.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace blackcube\core\helpers;

/**
 * Simple Matrix 2x2 tools
 *
 *  Matrix | a, b | is written in array [a, b, c, d]
 *         | c, d |
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class MatrixHelper
{
    /**
     * @var number a11
     */
    public $a;

    /**
     * @var number a12
     */
    public $b;

    /**
     * @var number a21
     */
    public $c;

    /**
     * @var number a22
     */
    public $d;

    /**
     * MatrixHelper constructor.
     * @param array $data array must be in format [a, b, c, d]
     */
    public function __construct(array $data)
    {
        list($this->a, $this->b, $this->c, $this->d) = $data;
    }

    /**
     * @return number matrix determinant
     * @since XXX
     */
    public function getDeterminant()
    {
        return ($this->a * $this->d) - ($this->b * $this->c);
    }

    /**
     * @param MatrixHelper|number $element element to multiply to the matrix
     * @since XXX
     */
    public function multiply($element)
    {
        if ($element instanceof MatrixHelper) {
            $resultMatrix = [
                $this->a * $element->a + $this->b * $element->c,
                $this->a * $element->b + $this->b * $element->d,
                $this->c * $element->a + $this->d * $element->c,
                $this->c * $element->b + $this->d * $element->d,
            ];
            list($this->a, $this->b, $this->c, $this->d) = $resultMatrix;
        } else {
            $this->a *= $element;
            $this->b *= $element;
            $this->c *= $element;
            $this->d *= $element;
        }
    }

    /**
     * Transform matrix to its own adjugate
     * @since XXX
     */
    public function adjugate()
    {
        $resultMatrix = [
            $this->d, -1 * $this->b,
            -1 * $this->c, $this->a,
        ];
        list($this->a, $this->b, $this->c, $this->d) = $resultMatrix;
    }

    /**
     * Transform matrix to its own inverse
     * @since XXX
     */
    public function inverse()
    {
        $determinant = $this->getDeterminant();
        $this->adjugate();
        $this->multiply(1 / $determinant);
    }

    /**
     * Transpose matrix
     * @since XXX
     */
    public function transpose()
    {
        $resultMatrix = [
            $this->a, $this->c,
            $this->b, $this->d,
        ];
        list($this->a, $this->b, $this->c, $this->d) = $resultMatrix;
    }

    /**
     * Convert matrix to array
     * @return array
     * @since XXX
     */
    public function toArray()
    {
        return [
            $this->a, $this->b,
            $this->c, $this->d,
        ];
    }
}

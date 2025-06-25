<?php
/**
 * PreviewManager.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace blackcube\core\interfaces;

/**
 * Preview manager interface
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

interface PreviewManagerInterface
{
    /**
     * @return boolean
     * @since XXX
     */
    public function check();

    /**
     * activate preview
     * @since XXX
     */
    public function activate();

    /**
     * deactivate preview
     * @since XXX
     */
    public function deactivate();

    /**
     * Get simulation date (to replace NOW() in SQL Requests)
     * @return string|null
     * @since XXX
     */
    public function getSimulateDate();

    /**
     * Define simulation date (to replace NOW() in SQL Requests)
     * @param string|null $startDate should be an sql date AAAA-MM-JJ or datetime AAAA-MM6JJ HH:MM:SS
     * @since XXX
     */
    public function setSimulateDate($simulateDate = null);

}

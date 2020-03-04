<?php
/**
 * PreviewManager.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 */

namespace blackcube\core\interfaces;

/**
 * Preview manager interface
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 */

interface PreviewManager
{
    /**
     * @return boolean
     */
    public function check();

    /**
     * activate preview
     */
    public function activate();

    /**
     * deactivate preview
     */
    public function deactivate();

    /**
     * Get simulation date (to replace NOW() in SQL Requests)
     * @return string|null
     */
    public function getSimulateDate();

    /**
     * Define simulation date (to replace NOW() in SQL Requests)
     * @param string|null $startDate should be an sql date AAAA-MM-JJ or datetime AAAA-MM6JJ HH:MM:SS
     */
    public function setSimulateDate($simulateDate = null);

}

<?php
/**
 * PreviewManager.php
 *
 * PHP version 8.0+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
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
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 * @since XXX
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

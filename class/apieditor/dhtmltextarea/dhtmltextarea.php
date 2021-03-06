<?php
/**
 * API dhtmltextarea class
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 API Project (www.api.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          editor
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('API_ROOT_PATH') || exit('Restricted access');

api_load('APIEditor');

/**
 * FormDhtmlTextArea
 *
 * @package
 * @author              John
 * @copyright       (c) 2000-2016 API Project (www.api.org)
 * @access              public
 */
class FormDhtmlTextArea extends APIEditor
{
    /**
     * Hidden text
     *
     * @var string
     * @access private
     */
    public $_hiddenText = 'apiHiddenText';

    /**
     * FormDhtmlTextArea::__construct()
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->rootPath = '/class/apieditor/' . basename(__DIR__);
        $hiddenText     = isset($this->configs['hiddenText']) ? $this->configs['hiddenText'] : $this->_hiddenText;
        api_load('APIFormDhtmlTextArea');
        $this->renderer = new APIFormDhtmlTextArea('', $this->getName(), $this->getValue(), $this->getRows(), $this->getCols(), $hiddenText, $this->configs);
    }

    /**
     * FormDhtmlTextArea::render()
     *
     * @return string
     */
    public function render()
    {
        return $this->renderer->render();
    }
}

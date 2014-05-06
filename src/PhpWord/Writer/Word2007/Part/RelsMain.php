<?php
/**
 * This file is part of PHPWord - A pure PHP library for reading and writing
 * word processing documents.
 *
 * PHPWord is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @link        https://github.com/PHPOffice/PHPWord
 * @copyright   2010-2014 PHPWord contributors
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord\Writer\Word2007\Part;

use PhpOffice\PhpWord\Shared\XMLWriter;

/**
 * Word2007 main relationship writer
 *
 * @since 0.11.0
 */
class RelsMain extends Rels
{
    /**
     * Write _rels/.rels
     */
    public function write()
    {
        $xmlRels = array(
            'docProps/core.xml' => 'package/2006/relationships/metadata/core-properties',
            'docProps/app.xml'  => 'officeDocument/2006/relationships/extended-properties',
            'word/document.xml' => 'officeDocument/2006/relationships/officeDocument',
        );
        $xmlWriter = $this->getXmlWriter();
        $this->writeRels($xmlWriter, $xmlRels);

        return $xmlWriter->getData();
    }
}
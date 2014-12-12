<?php

namespace Frontend\Modules\ContentBlocks\Engine;

use Frontend\Core\Engine\Model as FrontendModel;
use Backend\Modules\ContentBlocks\Entity\ContentBlock;

/**
 * In this file we store all generic functions that we will be using in the content_blocks module
 *
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Tijs verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class Model
{
    const ENTITY_CLASS = 'Backend\Modules\ContentBlocks\Entity\ContentBlock';

    /**
     * Get an item.
     *
     * @param string $id The id of the item to fetch.
     * @return array
     */
    public static function get($id)
    {
        $em = FrontendModel::get('doctrine.orm.entity_manager');
        return $em
            ->getRepository(self::ENTITY_CLASS)
            ->findOneBy(
                array(
                    'id'       => $id,
                    'status'   => ContentBlock::STATUS_ACTIVE,
                    'language' => FRONTEND_LANGUAGE,
                )
            )
        ;
    }
}

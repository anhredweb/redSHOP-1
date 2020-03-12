<?php

/**
 * @package     RedShop
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2020 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Redshop\Workflow;

use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;

defined('_JEXEC') or die;

/**
 * Workflow Helper
 *
 * @since __DEPLOY_VERSION__
 */
class Helper
{
    public static function init()
    {
        $definitionBuilder = new DefinitionBuilder();
        $definition = $definitionBuilder->addPlaces(['draft', 'reviewed', 'rejected', 'published'])
            // Transitions are defined with a unique name, an origin place and a destination place
            ->addTransition(new Transition('to_review', 'draft', 'reviewed'))
            ->addTransition(new Transition('publish', 'reviewed', 'published'))
            ->addTransition(new Transition('reject', 'reviewed', 'rejected'))
            ->build();

        $singleState = true; // true if the subject can be in only one state at a given time
        $property = 'currentState'; // subject property name where the state is stored
        $marking = new MethodMarkingStore($singleState, $property);
        $workflow = new Workflow($definition, $marking);

        return $workflow;
    }
}

<?php namespace Pz\Doctrine\Rest\Action\Relationships;

use Pz\Doctrine\Rest\Action\Related\RelatedCollectionAction;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\RestResponse;

class RelationshipsCollectionAction extends RelatedCollectionAction
{
    /**
     * @param RestRequestContract $request
     *
     * @return RestResponse
     */
    protected function handle($request)
    {
        $request->isRelationships(true);
        return parent::handle($request);
    }
}

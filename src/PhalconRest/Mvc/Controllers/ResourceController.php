<?php

namespace PhalconRest\Mvc\Controllers;

use PhalconRest\Api\ApiResource as ApiResource;
use PhalconRest\Constants\Services;
use PhalconRest\Transformers\ModelTransformer;
use Phalcon\Http\Response\StatusCode;

class ResourceController extends CollectionController
{
    protected function createResourceCollectionResponse($collection, $meta = null)
    {
        return $this->createCollectionResponse($collection, $this->getTransformer(),
            $this->getResource()->getCollectionKey(),
            $meta);
    }

    protected function getTransformer()
    {
        $transformerClass = $this->getResource()->getTransformer();
        $transformer = new $transformerClass();

        if ($transformer instanceof ModelTransformer) {
            $transformer->setModelClass($this->getResource()->getModel());
        }

        return $transformer;
    }

    /**
     * @return ApiResource
     */
    public function getResource()
    {
        $collection = $this->getCollection();
        if ($collection instanceof ApiResource) {
            return $collection;
        }

        return null;
    }

    protected function createResourceResponse($item, $meta = null, int $httpStatus = StatusCode::OK)
    {
        return $this->createItemResponse(
            $item,
            $this->getTransformer(),
            $this->getResource()->getItemKey(),
            $meta,
            $httpStatus
        );
    }

    protected function createResourceOkResponse($item, $meta = null, int $httpStatus = StatusCode::OK)
    {
        $this->setResponseHttpStatus($httpStatus);

        return $this->createItemOkResponse($item, $this->getTransformer(), $this->getResource()->getItemKey(), $meta);
    }

    protected function createItemCreatedResponse(string $locationHeader, int $httpStatus = StatusCode::CREATED)
    {
        $this->createResponseWithOutContent($locationHeader, $httpStatus);
    }

    protected function createItemUpdatedResponse(string $locationHeader, int $httpStatus = StatusCode::NO_CONTENT)
    {
        $this->createResponseWithOutContent($locationHeader, $httpStatus);
    }

    protected function createResponseWithOutContent(string $locationHeader, int $httpStatus = StatusCode::NO_CONTENT)
    {
        $this->setResponseHttpStatus($httpStatus);
        $this->setResponseHeader("Location", $locationHeader);
    }

    protected function createArrayResponse($array, $key, int $httpStatus = StatusCode::OK)
    {
        $this->setResponseHttpStatus($httpStatus);

        return parent::createArrayResponse($array, $key);
    }

    protected function createItemResponse(
        $item,
        $transformer,
        $resourceKey = null,
        $meta = null,
        int $httpStatus = StatusCode::OK
    ) {
        $this->setResponseHttpStatus($httpStatus);

        return parent::createItemResponse($item, $transformer, $resourceKey, $meta);
    }

    private function setResponseHttpStatus(int $httpStatus)
    {
        $response = $this->di->getShared(Services::RESPONSE);
        $response->setStatusCode($httpStatus);
    }

    private function setResponseHeader(string $headerName, string $headerValue)
    {
        $response = $this->di->getShared(Services::RESPONSE);
        $response->setHeader($headerName, $headerValue);
    }
}

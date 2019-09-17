<?php

namespace Mooon\Rest\EventSubscriber\Response;


use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Mooon\Rest\Annotation\Rest;
use Mooon\Rest\Converter\ObjConverter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class ResponseSerializationSubscriber
 */
class ResponseSerializationSubscriber implements EventSubscriberInterface
{
    const DEFAULT_RESPONSE_ARRAY = [
        'status' => [
            'code' => null,
            'message' => null,
        ],
        'data' => null,
        'trace' => null,
    ];

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * ResponseSerialization constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents():array
    {
        return [
            KernelEvents::VIEW => [
                ['onKernelView', 1],
            ],
        ];
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event):void
    {
        $result = $event->getControllerResult();

        /** @var Rest $annotation */
        $annotation = $event->getRequest()->attributes->get('_rest');

        $data = [];

        $responseConstants = (new ObjConverter())->constantsToArray(new Response());

        $data['status']['code'] = $annotation->getStatus();
        $data['status']['type'] = str_replace('HTTP_', '', $responseConstants[$annotation->getStatus()]);
        $data['status']['message'] = $annotation->getMessage();

        if(is_object($result)){
            $context = new SerializationContext();
            $context->setGroups($annotation->getGroups());
            $data['data'] = [];
            $data['data']['type'] = get_class($result);
            $data['data']['entity'] = json_decode($this->serializer->serialize($result, 'json', $context, $annotation->getType()), true);
        }elseif(is_array($result)){
            $arr = [];
            $isCollection = true;
            if(sizeof($result) <= 0){
                $isCollection = false;
            }

            $firstItemType = null;

            foreach ($result as $item){
                if(!is_object($item)){
                    $isCollection = false;
                    continue;
                }

                if(null === $firstItemType){
                    $firstItemType = get_class($item);
                }

                if($firstItemType !== get_class($item)){
                    $isCollection = false;
                }

                $context = new SerializationContext();
                $context->setGroups($annotation->getGroups());
                $arr[] = json_decode($this->serializer->serialize($item, 'json', $context, $annotation->getType()), true);
            }
            if(false === $isCollection){
                $data['data'] = $result;
            }else {
                $data['data']['entity'] = $arr;
                $data['data']['type'] = $firstItemType;
            }
        }else {
            $data['data'] = $result;
        }


        $data['trace'] = hash('sha256', uniqid(true));

        $event->setResponse(new JsonResponse($data, $annotation->getStatus()));
    }
}
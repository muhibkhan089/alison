<?php
namespace Magenest\Moneris\Gateway\Http\Converter;

use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magento\Framework\Xml\Parser as XmlParser;
use Psr\Log\LoggerInterface;

/**
 * Class XmlToArray
 *
 * @package Magenest\Moneris\Gateway\Http\Converter
 */
class XmlToArray implements ConverterInterface
{

    /**
     * @var XmlParser
     */
    private $parser;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * XmlToArray constructor.
     *
     * @param XmlParser $parser
     * @param LoggerInterface $logger
     */
    public function __construct(
        XmlParser $parser,
        LoggerInterface $logger
    ) {
        $this->parser = $parser;
    }

    /**
     * Converts gateway response to ENV structure
     *
     * @param mixed $response
     * @return array
     * @throws ConverterException
     */
    public function convert($response)
    {
        try {
            $this->parser->loadXML($response);
        } catch (\Exception $e) {
            throw new ConverterException(__('Can\'t read response from Moneris'));
        }
        $result = $this->parser->xmlToArray();
        if (!empty($result['response']['receipt'])) {
            return $result['response']['receipt'];
        } elseif (!empty($result['response'])) {
            return $result['response'];
        } else {
            $this->logger->debug('Can\'t read response from Moneris');
            throw new ConverterException(__('Can\'t read response from Moneris'));
        }
    }
}

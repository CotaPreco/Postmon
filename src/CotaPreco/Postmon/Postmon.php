<?php

/*
 * Copyright (c) 2015 Cota Preço
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace CotaPreco\Postmon;

use CotaPreco\Postmon\Exception\CepNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Psr7\Request;

/**
 * @author Andrey K. Vital <andreykvital@gmail.com>
 */
class Postmon implements PostmonInterface
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @param ClientInterface $httpClient
     */
    public function __construct(ClientInterface $httpClient = null)
    {
        $this->httpClient = $httpClient ?: new Client([
            'base_uri' => 'http://api.postmon.com.br/v1/'
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function findAddressByCep(Cep $cep)
    {
        try {
            $request = new Request('GET', 'cep/' . $cep);

            $response = $this->httpClient->send($request);

            /* @var \string[][] $json */
            $json = json_decode($response->getBody(), true);

            return new PartialAddress(
                $json['estado_info']['nome'],
                $json['cidade'],
                isset($json['bairro']) ? $json['bairro'] : null,
                isset($json['logradouro']) ? $json['logradouro'] : null,
                isset($json['complemento']) ? $json['complemento'] : null
            );
        } catch (\Exception $e) {
        }

        throw CepNotFoundException::forCep($cep);
    }
}

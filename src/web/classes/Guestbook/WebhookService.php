<?php

namespace Guestbook;

class WebhookService
{
    protected $couchdb_handle;

    public function __construct(\GuzzleHttp\Client $couchdb_handle) {
        $this->couchdb_handle = $couchdb_handle;
    }

    public function fetch() {
        $webhooks = [];

        $response = $this->couchdb_handle->request(
            "GET",
            "/webhooks/_all_docs",
            ['query' => ['include_docs' => 'true']]
        );
        if($response->getStatusCode() == 200) {
            if(false !== $data = json_decode($response->getBody(), true)) {
                foreach($data['rows'] as $row) {
                    $webhooks[] = $row['doc'];
                }
            }
        }

        return $webhooks;
    }

    public function add($webhook) {
        $response = $this->couchdb_handle->request(
            "POST",
            "/webhooks",
            [
                "json" => $webhook
            ]
        );

        return $response;
    }

    public function delete($webhook) {
        $response = $this->couchdb_handle->request(
            "DELETE",
            "/webhooks/" . $webhook['id'],
            [
                "query" => ["rev" => $webhook['rev']],
            ]
        );

        return $response;
    }
}

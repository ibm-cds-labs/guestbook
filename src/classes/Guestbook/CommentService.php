<?php

namespace Guestbook;

class CommentService
{
    protected $couchdb_handle;

    public function __construct(\GuzzleHttp\Client $couchdb_handle) {
        $this->couchdb_handle = $couchdb_handle;
    }

    public function fetch() {
        $comments = [];

        $response = $this->couchdb_handle->request(
            "GET",
            "/comments/_all_docs",
            ['query' => ['include_docs' => 'true']]
        );
        if($response->getStatusCode() == 200) {
            if(false !== $data = json_decode($response->getBody(), true)) {
                foreach($data['rows'] as $row) {
                    $comments[] = $row['doc'];
                }
            }

        }

        return $comments;
    }

    public function add($comment) {
        // add a timestamp field also
        $comment['time'] = time();
        $response = $this->couchdb_handle->request(
            "POST",
            "/comments",
            [
                "headers" => ["Content-Type" => "application/json"],
                "body" => json_encode($comment)
            ]
        );

        return $response;
    }
}
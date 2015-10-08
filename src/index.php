<?php
/**
 * Created by PhpStorm.
 * User: andrewscheerenberger
 * Date: 10/1/15
 * Time: 6:21 PM
 */
    require_once __DIR__ . '/../vendor/autoload.php';

    use Silex\Application;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;

    $app = new Application();

    $app['debug'] = true;

    // read the notes from disk
    $notes = [
        '67f610abf00c9' => [
            'name' => 'PHP Basics',
            'body' => 'PHP is a very fast way to quickly code high performant APIs',
            'username' => 'Shadowboxin',
            'tags' => 'php,api,code'
        ],
        '68f610abf00d9' => [
            'name' => 'Silex Framework',
            'body' => 'Silex is great way to learn PHP frameworks',
            'username' => 'Alpha',
            'tags' => 'php,framework,silex,code'
        ]
    ];


    //read users from disk
    $users = [
      '98as7diusd65464' => [
          'username' => 'Lil B',
          'password' => 'Swagzilla',
          'fullname' => 'Bradon Christopher McCartney'
      ],
      '1sd7987asdsasd43' =>[
           'username' => 'Shadowboxin',
           'password' => 'SpecialTechnique',
           'fullname' => 'Andrew Scheerenberger'
      ]
    ];

    $app->get('/', function() {
        return new Response('Project 1 - POC API', 200);
    });

    $app->get('/notes', function() use ($notes){
        return new Response(
            json_encode($notes),
            200,
            ['Content-Type' => 'application/json']
        );
    });

    $app->get('/notes/{id}', function (Application $app, $id) use ($notes) {
        if(!isset($notes[$id]))
        {
            $app->abort(404, "Note with ID {id} does not exist.");
        }

        return new Response(
          json_encode($notes[$id]),
            200,
            ['Content-Type' => 'application/json']
        );

    });

    $app->put( '/notes/{id}', function (Application $app, Request $request, $id) use ($notes) {
        if(!isset($notes[$id]))
        {
            $app->abort(404, "Note with ID {id} does not exist.");
        }

        $payload = json_decode($request->getContent());

        $notes[$id] = $payload;

        return new Response(
            json_encode($notes),
            200,
            ['Location' => 'http://localhost:8888/notes/'.$id]
        );


    });

    $app->delete('/notes/{id}', function (Application $app, $id) use ($notes) {
        if(!isset($notes[$id]))
        {
            $app->abort(404, "Note with ID {id} does not exist.");
        }
        unset($notes[$id]);

     return new Response(null, 204);

    });

    $app->post('/notes', function (Application $app, Request $request) use ($notes) {
        $contentTypeValid = in_array(   // checks content types and then give 406 error code if its incorrect type
                'application/json',
                $request->getAcceptableContentTypes()
        );

        if (!$contentTypeValid)
        {
            $app->abort(406, 'Client must accept content type of "application/json"');
        }

        $content = json_decode($request->getContent()); // the content of the request is gotten

        $newId = uniqid(); // makes a key for the new value to be input

        $notes[$newId] = [
            'name' => $content->name,
            'body' => $content->body,
            'username' => $content->username,
            'tags' => $content->tags
        ];

        //write notes to disk

        return new Response(
            json_encode($notes),
            201,
            ['Location' => 'http://localhost:8888/notes/'.$newId]
        );

    });

    //todo, implement create, update and delete for user
    $app->post('/users', function (Application $app, Request $request) use ($users) {
    $contentTypeValid = in_array(   // checks content types and then give 406 error code if its incorrect type
        'application/json',
        $request->getAcceptableContentTypes()
    );

    if (!$contentTypeValid)
    {
        $app->abort(406, 'Client must accept content type of "application/json"');
    }

    $content = json_decode($request->getContent()); // the content of the request is gotten

    $newId = uniqid(); // makes a key for the new value to be input

    $notes[$newId] = [
        'username' => $content->username,
        'password' => $content->password,
        'fullname' => $content->fullname
    ];

    //write notes to disk

    return new Response(
        json_encode($notes),
        201,
        ['Location' => 'http://localhost:8888/users/'.$newId]
    );

    });

    $app->delete('/users/{id}', function (Application $app, $id) use ($users) {
        if(!isset($users[$id]))
        {
            $app->abort(404, "Note with ID {id} does not exist.");
        }
        unset($users[$id]);

        return new Response(null, 204);

    });

    $app->put( '/users/{id}', function (Application $app, Request $request, $id) use ($users) {
        if(!isset($users[$id]))
        {
            $app->abort(404, "Note with ID {id} does not exist.");
        }

        $payload = json_decode($request->getContent());

        $users[$id] = $payload;

        return new Response(
            json_encode($users),
            200,
            ['Location' => 'http://localhost:8888/notes/'.$id]
        );


    });
    $app->run();



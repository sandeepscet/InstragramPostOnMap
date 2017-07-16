<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Vinkla\Instagram\Instagram;
use GuzzleHttp\Client;
use App\Posts;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home')->with(array('googleMapURL' => env('GOOGLE_MAP_SCRIPT_URL') , 'googleMapApiKey' => env('GOOGLE_MAP_API_KEY')));
    }

    public function list()
    {
        return view('list');
    }

    public function saveInstaPost(Request $request)
    {
        $data = $request->input('post');

        return  Posts::create([
            'email' =>  Auth::user()->email,
            'postId' => $data['id'],
            'caption' => utf8_encode($data['caption']),
            'post' => json_encode($data)
        ]);
    }

    public function listInstaPost(Request $request)
    {
        $searchTerm = $request->input('searchTerm');
        $sortOrder= $request->input('sortOrder');

        $query = Posts::where('email', Auth::user()->email);
        if($searchTerm)
        {
            $query = $query->where('caption','like',  '%' .$searchTerm. '%');
        }
        if($sortOrder)
        {
            $query = $query->orderBy('created_at', $sortOrder);
        }
        else
         {
            $query = $query->orderBy('created_at', 'desc');  
         }  




        $DbPosts = $query->get();
        $posts = array();
        foreach ($DbPosts as $dbPost) {
            $postarr = json_decode($dbPost['attributes']['post'], true);
            array_push($posts, $postarr);
        }
        return response()->json($posts, 200);
    }

    public function getInstaPost(Request $request)
    {
        $instagram = new Instagram(); 
        $client = new Client();
        $posts = $instagram->get($request->input('instaUserName'));

        $filteredPostData = array();

        $postIds = array();
        for($index = 0 ;$index < count($posts); $index++)
        {
            $filteredPost = array();

            $filteredPost['id'] = $posts[$index]->id;
            $filteredPost['thumbnail'] = $posts[$index]->images->thumbnail;
            $filteredPost['standard_resolution'] = $posts[$index]->images->standard_resolution;
            $filteredPost['caption'] = $posts[$index]->caption->text;
            $filteredPost['type'] = $posts[$index]->type;
            
           
            if($posts[$index]->location && $posts[$index]->location->name)
            {
                $locationName = $posts[$index]->location->name;
                $res = $client->request('GET',env('GOOGLE_GEO_CODE_URL').'?address='.$locationName.'&key='.env('GOOGLE_GEO_CODE_KEY'));
                $result = json_decode($res->getBody()->getContents());

                $filteredPost['location']['name'] = $locationName;
                $filteredPost['location']['lat'] = $result->results[0]->geometry->location->lat;
                $filteredPost['location']['lng'] = $result->results[0]->geometry->location->lng;
            }

            if($posts[$index]->type == 'video')
            {
                 $filteredPost['low_resolution'] = $posts[$index]->videos->low_resolution;
            }
            

            array_push($filteredPostData, $filteredPost);
            array_push($postIds, $filteredPost['id']);
        }

        $DbPosts = Posts::whereIn('postId',$postIds)->get();
        
         $savedPostIds = array();
        foreach ($DbPosts as $dbPost) {
            array_push($savedPostIds, $dbPost['attributes']['postId']);
        }

        for($index = 0 ;$index < count($filteredPostData); $index++)
        {
            $filteredPostData[$index]['saved'] = in_array($filteredPostData[$index]['id'], $savedPostIds);
        }

        return response()->json($filteredPostData, 200);
     
    }
    
}

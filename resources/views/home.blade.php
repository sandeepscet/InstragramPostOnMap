@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    <div id="my_view">
                        <div id="searchDiv">
                            <form class="form-horizontal" onsubmit="return false">
                                <div class="col-xs-4">
                                    <input type="text" name="instaUserName" v-model="instaUserName" value="katyperry" required class="form-control" />
                                  </div>
                                  <div class="col-xs-2">
                                    <button v-on:click="searchInstaPosts" id="getPost" class="btn  btn-primary">
                                      Get Post
                                    </button>
                                    <span v-show="loading">Loading</span>
                                  </div>                                
                            </form>
                        </div>
                    </div>
                
                  <br />
                  <br />
                  <div class="col-md-6">
                    <ul id="listPosts" style="display: none" class="list-group">
                        <li v-for="post in posts" class="list-group-item">
                            @{{ post.caption | truncate 50}}
                            <button v-on:click="locateInstaPosts(post)" v-if="post.location" type="button" class="btn btn-primary">Locate</button>
                            <button v-on:click="saveInstaPosts(post, $event)" v-if="post.saved == false" type="button" class="btn btn-primary">Save</button>
                        </li>
                    </ul>
                  </div>

                  <div id="postMapdata" class="col-md-6" style="display: none">
                      <div id="map" style="margin: 5px;height: 400px;width: 400px"></div>
                  </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

var googleMapScriptUrl = "{{ $googleMapURL }}";
var googleMapApiKeys = "{{ $googleMapApiKey }}";

Vue.filter('truncate', function(text, stop, clamp) {
    return text.slice(0, stop) + (stop < text.length ? clamp || '...' : '')
})

var myViewModel = new Vue({
    el: '#my_view',
    data: {
            loading: false
        },
    methods: {
        searchInstaPosts: function() {
            var vueThis = this;
            vueThis.$set('loading', true);
            getInstaPostData(this.$get('instaUserName'), function(posts) {
                if (posts.length) {
                    listPosts(posts);
                    $('#postMapdata').show();

                    var postWithLocation = [];
                    for (var i = 0; i < posts.length; i++) {
                        if (posts[i].location) {
                            postWithLocation.push(posts[i]);
                        }
                    }

                    if (postWithLocation.length) {
                        renderGoogleMap(postWithLocation);
                    } 
                } else {
                    alert('Post for mentioned username not found');
                }
               
                vueThis.$set('loading', false);
            });
        }
    }
});

var vueListPost = new Vue({
        el: '#listPosts',
        data: {
            posts: []
        },
        methods: {
            saveInstaPosts: function(post, event) {
                saveInstaPostData(post, function(response) {
                    alert('Post saved');
                    event.target.style.display = 'none';
                })
            },
            locateInstaPosts: function(post) {
                if (post.location) {
                    locatePostOnMap(post);                    
                } else {
                    alert('No location found with this post');
                }
            }
        }
    })

function addMarkers(map, data) {
    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(data.location.lat, data.location.lng),
        map: map
    });

    google.maps.event.addListener(marker, 'click', function() {
        var infowindow = new google.maps.InfoWindow();
        var contentString = '<div data-postId="' + data.id + '" class="infoMarker"><strong>' + data.caption + '</strong><img width="' + data.thumbnail.width + '" height="' + data.thumbnail.height + '" src="' + data.thumbnail.url + '" /></div>';
        infowindow.setContent(contentString);
        infowindow.open(map, this);
    });
}

function locatePostOnMap(post) {
    var map = new google.maps.Map(document.getElementById('map'), {
        center: {
            lat: post.location.lat,
            lng: post.location.lng
        },
        zoom: 15
    });
    addMarkers(map, post);
}

function renderGoogleMap(posts) {
    var googleMapScript = googleMapScriptUrl+"?key="+googleMapApiKeys+"&libraries=places";

    loadScript(googleMapScript, function() {
        initMap(posts);
    });
}

function initMap(posts) {
    var map = new google.maps.Map(document.getElementById('map'), {
        center: {
            lat: posts[0]['location']['lat'],
            lng: posts[0]['location']['lng']
        },
        zoom: 15
    });

    for (var i = 0; i < posts.length; i++) {
        addMarkers(map, posts[i]);
    }
}

function getInstaPostData(instaUserName = '', callback) {
    $.ajax({
        url: '/home/getInstaPost',
        data: {
            instaUserName: instaUserName,
            "_token": "{{ csrf_token() }}",
        },
        method: 'GET'
    }).then(function(posts) {
        callback(posts)

    })
}

function saveInstaPostData(post, callback) {
    $.ajax({
        url: '/home/saveInstaPost',
        data: {
            post: post,
            "_token": "{{ csrf_token() }}",
        },
        method: 'POST'
    }).then(function(posts) {
        callback(posts)

    })
}


function listPosts(posts) {
    $('#listPosts').show();
    vueListPost.$set('posts', posts);
}

function loadScript(src, callback) {
    var s,
        r,
        t;
    r = false;
    s = document.createElement('script');
    s.type = 'text/javascript';
    s.src = src;
    s.onload = s.onreadystatechange = function() {
        //console.log( this.readyState ); //uncomment this line to see which ready states are called.
        if (!r && (!this.readyState || this.readyState == 'complete')) {
            r = true;
            callback();
        }
    };
    t = document.getElementsByTagName('script')[0];
    t.parentNode.insertBefore(s, t);
}
</script>


@endsection

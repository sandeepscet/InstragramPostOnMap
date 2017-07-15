@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Listing</div>

                <div class="panel-body">
                    <div id="my_view">

                    <div id="searchDiv">
                       <input type="text" name="searchTerm" v-model="searchTerm"/>

                       <input type="radio" id="asc" value="asc" v-model="sortOrder" selected="selected">
                      <label for="asc">asc</label>
                      <input type="radio" id="desc" value="desc" v-model="sortOrder">
                      <label for="desc">desc</label>

                      <button v-on:click="searchPosts">Search</button>
                    </div>

                    <div id="gallaryBlock">
                            <div class="row">
                              <div class="gallery col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                  <h1 class="gallery-title">Gallery</h1>
                              </div>

                              <div v-for="post in posts" class="gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter sprinkle" style="margin-bottom: 30px;">
                                  <img v-bind:src=post.standard_resolution.url class="img-responsive"  height="@{{ post.standard_resolution.height" width="@{{ post.standard_resolution.width" alt="@{{ post.caption  | truncate 50}}">
                                      <p>@{{ post.caption  | truncate 50}}</p>
                              </div>
                              
                          </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">

Vue.filter('truncate', function (text, stop, clamp) {
        return text.slice(0, stop) + (stop < text.length ? clamp || '...' : '')
    })

var myModel = {
  posts: []
  };

  var myViewModel = new Vue({
    el: '#my_view',
    data: myModel,
      ready : function()
        {
          this.fetchInitialPosts();
    
        },
         methods:
        {
          fetchInitialPosts: function()
            {
              var $this = this;
              getPostData("", 'asc' , function(posts){
                  $this.$set('posts', posts);
                });
         } ,
        searchPosts: function()
            { 
              var $this = this;
              getPostData(this.$get('searchTerm'), this.$get('sortOrder') , function(posts){
                 $this.$set('posts', posts);
                });
        }
    }
  });

  function getPostData(searchTerm = '', sortOrder = 'true', callback)
  {
      $.ajax({
               url: '/home/listInstaPost',
                data: {searchTerm : searchTerm , sortOrder : sortOrder ,"_token": "{{ csrf_token() }}",},
          }).then(function (posts) {
              callback(posts)
              
          })
  }
</script>

@endsection

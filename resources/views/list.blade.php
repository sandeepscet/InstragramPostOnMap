@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Listing</div>

                <div class="panel-body">
                    <div id="my_view">

                    <form class="form-horizontal" onsubmit="return false;">
                      <fieldset>

                      <div class="form-group">
                        <label class="col-md-4 control-label" for="searchTerm">Search</label>  
                        <div class="col-md-4">
                        <input id="searchTerm" name="searchTerm" v-model="searchTerm" type="text" placeholder="Search in caption" class="form-control input-md">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-md-4 control-label" for="sortBy">Search radius</label>
                        <div class="col-md-4">
                          <select id="sortBy" name="radius" class="form-control" v-model="sortOrder">
                            <option value="asc">Ascending</option>
                            <option value="desc">Descending</option>
                          </select>
                        </div>
                      </div>

                      <div class="form-group">
                      <label class="col-md-4 control-label" for="submit"></label>
                      <div class="col-md-4">
                        <button id="submit" name="submit" class="btn btn-primary"  v-on:click="searchPosts">Search</button>
                      </div>
                    </div>

                      </fieldset>
                    </form>

                    <div id="gallaryBlock">
                            <div class="row">
                              <div class="gallery col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                  <h1 class="gallery-title">Gallery</h1>
                              </div>

                              <h1 v-if="posts.length == 0">No Post found.</h1>
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
  posts: [],
  sortOrder : 'desc'
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

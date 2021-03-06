
@extends('layouts.layout') 
@section('content')

<div class="outerContentContainer">
    @include('includes.messages')
    @include('includes.errors')
    <div class="innerContentContainer">
        <form data-abide novalidate class="adminform" action="" method="post" enctype="multipart/form-data">            

            <div class="small-12 columns">
                <h1>Create movie</h1>

                {{ csrf_field() }}

                <label></label>
                <input class="inputfield-grey-placeholder" type="text" name="title" placeholder="Movie title" required>
                <span class="form-error">Please fill in movie title.</span>

                <label for="uploadPosterNew" class="button">Upload poster</label>
                <input id="uploadPosterNew" type="file" name="poster" class="uploadPoster show-for-sr" required/>
                <span class="form-error">Please add movie poster.</span>

                <div class="foundation-bottom-margin">
                    <label></label> 
                    <select class="multi-select js-example-placeholder-multiple select2-full-width" name="genres[]" multiple="multiple" required>
                        <option></option>
                        @foreach ($genres as $genre)
                            <option value="{{ $genre["id"] }}">{{ $genre["genre_name"] }}</option>
                        @endforeach
                    </select>
                    <span class="form-error">
                    Don't forget to add genre.
                    </span>
                </div>
                            
                <!-- <select class="js-example-basic-single" name="releaseyear"> -->
                <!-- Add placeholder -->
                <label></label>
                <select class="release-year selectfield-grey-placeholder" name="releaseyear" required>
                    <option value="" disabled selected hidden>Choose year</option>
                    @foreach ($releaseyears as $releaseyear)
                        <option value="{{ $releaseyear }}">{{ $releaseyear }}</option>
                    @endforeach
                </select>
                <span class="form-error">Add release year.</span>

            <label></label>
                <input class="inputfield-grey-placeholder" type="number" name="playtimeMins" placeholder="Playtime minutes" required>
                <span class="form-error">Add playtime.</span>

            <label></label>
                <textarea class="inputfield-grey-placeholder" cols="30" rows="10" name="plot" placeholder="Movie plot"></textarea>
                <span class="form-error">Don't forget to write the movie plot.</span>

                @include('partials.personlist', ['choices' => $actors, 'type' => 'actor'])
                @include('partials.personlist', ['choices' => $directors, 'type' => 'director'])
                @include('partials.personlist', ['choices' => $producers, 'type' => 'producer'])
                
                <button class="button" type="submit">Create Movie</button>
            </div>
        </form>
    </div>
</div>
    
@endsection 


@section('page-scripts')
<script>
    $(document).ready(function() {
        // Select 2 script foundation
        $('.multi-select').select2({
            placeholder: "Choose Genre",
            theme: "default select2-container--full-width select2-container--large",
        });

        $('.personproducer').select2({
            placeholder: "Choose an existing producer", 
            theme: "default select2-container--with-add-button select2-container--large",
        });

        $('.persondirector').select2({
            placeholder: "Choose an existing director", 
            theme: "default select2-container--with-add-button select2-container--large",
        });

        $('.personactor').select2({
            placeholder: "Choose an existing actor", 
            theme: "default select2-container--with-add-button select2-container--large",
        });
    });
</script>
@endsection
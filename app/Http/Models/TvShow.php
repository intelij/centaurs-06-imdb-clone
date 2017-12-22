<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use DB;

class TvShow extends Model
{
    public function createTvSHowFromApi($tvShow)
    {
        if(!$this->ifTvShowExists($tvShow['name'])){
            DB::table('tv_shows')->insert([
                'title' => $tvShow['name'],
                'plot' => $tvShow['overview'],
                'poster' => $tvShow['poster_path'],
                'backdrop' => $tvShow['backdrop_path'],
                'releasedate' => $tvShow['first_air_date'],
                'imdb_rating' => $tvShow['vote_average'],
                'chas_rating' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function createSeasonsFromApi($season, $tvShow)
    {
        if(!$this->IfSeasonExists($tvShow->id, $season['season_number'])){
            DB::table('seasons')->insert([
                'season_number' => $season['season_number'],
                'tv_show_id' => $tvShow->id
            ]);
        }
    }

    public function createEpisodeFromApi($episodeInfo, $tvShowId, $seasons)
    {
        $season = $this->getTvShowSeason($episodeInfo['season_number'], $tvShowId);
        if(isset($season) && !$this->ifEpisodeExists($season->id, $episodeInfo['episode_number'])) {
            DB::table('episodes')->insert([
                'season_id' => $season->id,
                'episode_nr' => $episodeInfo['episode_number'],
                'title' => $episodeInfo['name'],
                'plot' => $episodeInfo['overview'],
                'playtime' => $seasons['episode_run_time'][0],
                'poster' => $episodeInfo['still_path'],
                'backdrop' => $seasons['backdrop_path'],
                'releasedate' => $episodeInfo['air_date'],
                'imdb_rating' => $episodeInfo['vote_average'],
                'chas_rating' => null
                ]);
        }
    }

    public function createEpisodeStaffFromApi($episodeCredits, $tvShowId, $episodeInfo)
    {
        $movieModel = new Movie();
        foreach ($episodeCredits['cast'] as $cast) {
            if(!$movieModel->ifActorExists($cast['name'])) {
                DB::table('actors')->insert([
                    'name' => $cast['name']
                    ]);
            }
            $actor = $movieModel->getActors($cast['name']);
            $season = $this->getTvShowSeason($episodeInfo['season_number'], $tvShowId);
            $episode = $this->getEpisode($season->id, $episodeInfo['episode_number']);
            
            if(!$this->ifActorEpisodeLedgerExists($actor->id, $episode->id)){
                DB::table('ledger_actors')->insert([
                    'actor_id' => $actor->id,
                    'episode_id' => $episode->id
                    ]);
            }
        } 
        foreach ($episodeCredits['crew'] as $crew) {
            if($crew['job'] === 'Director') {
                if(!$movieModel->ifDirectorExists($crew['name'])) {
                    DB::table('directors')->insert([
                        'name' => $crew['name']
                    ]);
                }

                $director = $movieModel->getDirectors($crew['name']);

                if(!$this->ifEpisodeDirectorLedgerExists($director->id, $episodeInfo['episode_number'])) {
                    DB::table('ledger_directors')->insert([
                        'director_id' => $director->id,
                        'episode_id' => $episodeInfo['episode_number']
                    ]);
                }
            }

            
        }
    }

    public function getEpisode($seasonId, $episodeNumber)
    {
        return DB::table('episodes')->where([
            'season_id' => $seasonId,
            'episode_nr'=> $episodeNumber
        ])->first();
    }

    public function getTvShowSeason($seasonNumber, $tvShowId)
    {   
        return DB::table('seasons')->where('season_number', $seasonNumber)->where('tv_show_id', $tvShowId)->first();
    }

    public function getTvShowByName($tvShowName)
    {
        return DB::table('tv_shows')->where('title', $tvShowName)->first();
    }
    
    public function ifTvShowExists($TvShowTitle): bool
    {
        return DB::table('tv_shows')->where('title', $TvShowTitle)->exists();
    }

    public function ifEpisodeExists($seasonId, $episodeNumber): bool
    {
        return DB::table('episodes')->where([
            'season_id' => $seasonId,
            'episode_nr'=> $episodeNumber])->exists();
    }
    
    public function IfSeasonExists($tvShowId, $seasonNumber): bool
    {
        return DB::table('seasons')->where('tv_show_id', $tvShowId)->where('season_number', $seasonNumber)->exists();
    }

    public function ifWriterEpisodeLedgerExists($writerId, $episodeId): bool
    {
        return DB::table('ledger_writers')->where('writer_id', $writerId)->where('episode_id', $episodeId)->exists();
    }
    
    public function ifEpisodeDirectorLedgerExists($directorId, $episodeId): bool
    {
        return DB::table('ledger_directors')->where('director_id', $directorId)->where('episode_id', $episodeId)->exists();
    }
    
    public function ifEpisodeProducerLedgerExists($producerId, $episodeId): bool
    {
        return DB::table('ledger_producers')->where('producer_id', $producerId)->where('episode_id', $episodeId)->exists();
    }

    public function ifActorEpisodeLedgerExists($actorId, $episodeId): bool
    {
        return DB::table('ledger_actors')->where('actor_id', $actorId)->where('episode_id', $episodeId)->exists();
    }
}

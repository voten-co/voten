<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
	// onDelete we must also delete the actual photos from the FTP server
	// onDelete we must also delete the actual photos from the FTP server
	// onDelete we must also delete the actual photos from the FTP server

    /**
    *   The attributes that are mass assignable.
    *
    *   @var array
    */
    protected $fillable = [
        'submission_id'
    ];


	public function owner()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function ownedBy(User $user)
	{
		return $this->user_id == $user->id;
	}


	public function submission()
	{
		return $this->belongsTo(Submission::class, 'submission_id');
	}

}

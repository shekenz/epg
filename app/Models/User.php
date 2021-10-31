<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
	use HasFactory, Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
			'username',
			'lastname',
			'firstname',
			'email',
			'birthdate',
			'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
			'password',
			'remember_token',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
			'email_verified_at' => 'datetime',
	];



	// model relationship
	public function media() {
			return $this->hasMany(Medium::class)->orderBy('created_at', 'DESC');
	}



	public function bookInfos() {
      return $this->hasMany(BookInfo::class);
  }



}

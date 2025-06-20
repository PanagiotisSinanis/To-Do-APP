<?php

namespace App\Models;

// Εισάγουμε το trait του Sanctum που επιτρέπει τη δημιουργία API tokens
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // Εδώ "ενεργοποιούμε" το Sanctum με το trait HasApiTokens
    use HasApiTokens, HasFactory, Notifiable;

    // Ορίζουμε το όνομα του πίνακα που θα χρησιμοποιεί το μοντέλο (προαιρετικό αν είναι "users")
    protected $table = "users";

    /**
     * Τα πεδία που μπορούμε να κάνουμε mass assign (όταν πχ δημιουργούμε ή ενημερώνουμε χρήστες)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Τα πεδία που κρύβονται όταν κάνουμε το μοντέλο JSON (πχ δεν θες να φαίνεται το password στο API response)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Εδώ ορίζουμε τι μετατροπές θέλουμε στα δεδομένα (πχ ημερομηνίες)
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Laravel 10+ auto-hash όταν κάνεις assign το password
        ];
    }
}

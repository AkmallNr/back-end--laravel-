namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admins'; // Pastikan ini sesuai dengan nama tabel di database

    protected $fillable = ['username', 'password'];

    protected $hidden = ['password', 'remember_token'];
}
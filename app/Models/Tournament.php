// Add to app/Models/Tournament.php (Member 01's file — this is the ONE line
// this feature needs from a shared file; raise it as a small dedicated PR
// and tag Member 01 for review, per the ownership rule).
public function livestreams(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(Livestream::class);
}
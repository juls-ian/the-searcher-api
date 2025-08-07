# Scrapped codes in the User Model 


## activeEditorialBoard() - to get the active ed board
    public function activeEditorialBoard()
    {
        return $this->hasOne(EditorialBoard::class)->where('is_active', true);
    }

## currentEditorialBoard - to get the latest ed bord
    public function currentEditorialBoard()
    {
        return $this->hasOne(EditorialBoard::class)->latest();
    }
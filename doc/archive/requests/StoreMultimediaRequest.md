# Unused codes in the StoreMultimediaRequest


## v.1
    protected function prepareForValidation()
    {
        // Convert single file to array if needed 
        if ($this->hasFile('files')) {
            $files = $this->file('files');

            // Convert into array if not array 
            if (!is_array($files)) {

                $this->files->set('files', [$files]);
            }
        }
    }


# v.2
    protected function prepareForValidation()
    {
        // Handle the files conversion before validation
        if ($this->hasFile('files')) {
            $files = $this->file('files');

            // If files is not an array (single file or multiple files sent as single key)
            if (!is_array($files)) {
                // Convert single file to array
                $filesArray = [$files];
            } else {
                $filesArray = $files;
            }

            // Replace the files in the request
            $this->files->replace(['files' => $filesArray] + $this->files->all());
        }
    }
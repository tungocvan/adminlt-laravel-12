<div>
    <h3>EXAMPLE FORM</h3>

    <div class="row">
       
            <div class="col-md-4" x-data="{message: 'Texas' }">
                <select class="form-control" x-model="message">
                    <option>Alabama</option>
                    <option>Alaska</option>
                    <option>California</option>
                    <option>Delaware</option>
                    <option>Tennessee</option>
                    <option>Texas</option>
                    <option>Washington</option>
                  </select>
                <div x-text="message"></div>
                <button class="btn btn-primary" type="button" x-on:click="$wire.handleSubmit(message)">Submit</button>
            </div>  
            <div class="col-4">
                <div x-data="{ open: $wire.entangle('showDropdown') }">
                    <button x-on:click="open = true">Show More...</button>
                 
                    <ul x-show="open" x-on:click.outside="open = false">
                        <li><button wire:click="archive">Archive</button></li>
                 
                        <li><button wire:click="delete">Delete</button></li>
                    </ul>
                </div>
                <div>
                    <form wire:submit="save">
                    <input wire:model="title" type="text">
                    <small>
                        Character count: <span x-text="$wire.title.length"></span> 
                    </small>
                    <button type="button" x-on:click="$wire.title = ''">Clear</button> 
                    </form> 
                </div>
            </div>
              
    </div>

</div>
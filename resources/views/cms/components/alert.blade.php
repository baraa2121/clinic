@if (session('message'))
                
                <div class="alert alert-{{session('alert')}} alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h5><i class="icon fas @if(session('alert')=='success') fa-check @else fa-ban @endif"></i> Alert!</h5>
                 {{session('message')}}
                </div>
              @endif


<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<div class="left-side-bar">
    <div class="brand-logo">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('images/schaxist.png') }}" alt="Logo" class="dark-logo">
            <img src="{{ asset('images/schaxist.png') }}" alt="Logo" class="light-logo">
        </a>
        <div class="close-sidebar" data-toggle="left-sidebar-close">
            <i class="ion-close-round"></i>
        </div>
    </div>
    
    <div class="menu-block customscroll">
        <div class="sidebar-menu">
            <ul id="accordion-menu">
                {{-- Fixed Dashboard Menu Item --}}
                <li class="dropdown">
                    <a href="{{ route('dashboard') }}" class="dropdown-toggle no-arrow">
                        <span class="micon dw dw-house-1"></span>
                        <span class="mtext">Dashboard</span>
                    </a>
                </li>

                {{-- Dynamic Menu Items --}}
                @if(isset($menuItems) && !empty($menuItems))
                    @foreach($menuItems as $item)
                        <li class="{{ !empty($item['children']) ? 'dropdown' : '' }}">
                            <a href="{{ $item['href'] }}" 
                               class="dropdown-toggle {{ empty($item['children']) ? 'no-arrow' : '' }}">
                                
                                {{-- Icon Display --}}
                                @if(!empty($item['icon']))
                                    @if(Str::contains($item['icon'], ['.png', '.jpg', '.jpeg', '.svg']))
                                        {{-- Image Icon --}}
                                        <img src="{{ asset($item['icon']) }}" 
                                             alt="{{ $item['name'] }}" 
                                             class="micon" 
                                             style="width: 25px; height: 25px;">
                                    @else
                                        {{-- Font Icon --}}
                                        <span class="micon {{ $item['icon'] }}"></span>
                                    @endif
                                @else
                                    {{-- Default Icon --}}
                                    <span class="micon dw dw-library"></span>
                                @endif
                                
                                <span class="mtext">{{ $item['name'] }}</span>
                            </a>

                            {{-- Submenu --}}
                            @if(!empty($item['children']))
                                <ul class="submenu">
                                    @foreach($item['children'] as $child)
                                        <li>
                                            <a href="{{ $child['href'] }}">
                                                {{ $child['name'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                @else
                    <li>
                        <span class="mtext text-muted" style="padding-left: 20px;">
                            No menu items available
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
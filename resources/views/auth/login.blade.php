@extends('auth.main')
@section('content')
    <section class="fxt-template-animation fxt-template-layout4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 col-12 fxt-bg-wrap">
                    <div class="fxt-bg-img" data-bg-image="{{ asset('images/bgreset.jpg') }}">
                        <div class="fxt-header">
                            <div class="fxt-transformY-50 fxt-transition-delay-1">
                                <a href="/" class="fxt-logo"><img src="{{ asset('images/minlogo.png') }}"
                                        alt="Logo"></a>
                            </div>
                            <div class="fxt-transformY-50 fxt-transition-delay-2">
                                <h1>Selamat datang di Bantuin</h1>
                            </div>
                            <div class="fxt-transformY-50 fxt-transition-delay-3">
                                <p>Tempat terbaik buat kamu yang ingin mencari uang dengan membantu orang lain, dan tempat
                                    terbaik
                                    untuk kamu yang ingin mencari seseorang untuk membantu kamu!.
                                </p>
                            </div>
                        </div>
                        {{-- <ul class="fxt-socials">
                            <li class="fxt-facebook fxt-transformY-50 fxt-transition-delay-4"><a href="#"
                                    title="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                            <li class="fxt-twitter fxt-transformY-50 fxt-transition-delay-5"><a href="#" title="twitter"><i
                                        class="fab fa-twitter"></i></a></li>
                            <li class="fxt-google fxt-transformY-50 fxt-transition-delay-6"><a href="#" title="google"><i
                                        class="fab fa-google-plus-g"></i></a></li>
                            <li class="fxt-linkedin fxt-transformY-50 fxt-transition-delay-7"><a href="#"
                                    title="linkedin"><i class="fab fa-linkedin-in"></i></a></li>
                            <li class="fxt-youtube fxt-transformY-50 fxt-transition-delay-8"><a href="#" title="youtube"><i
                                        class="fab fa-youtube"></i></a></li>
                        </ul> --}}
                    </div>
                </div>
                <div class="col-md-6 col-12 fxt-bg-color">
                    <div class="fxt-content">
                        <div class="fxt-form">
                            <form method="post" action="/login" method="post">
                                <div class="form-group">
                                    @csrf
                                    @if (session()->has('failed'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            {{ session('failed') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    @endif

                                    @if (session()->has('success'))
                                        <div class="alert alert-success" role="alert">
                                            {{ session('success') }}
                                        </div>
                                    @endif
                                    <label class="col-form-label">Email</label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="text"
                                        name="email" id="email" required placeholder="Masukkan Email">
                                    <div class="show-hide" @error('email') style="padding-right: 10px" @enderror>
                                        <span class="show"></span>
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <label class="col-form-label mt-3">Password</label>
                                    <input class="form-control @error('password') is-invalid @enderror" type="password"
                                        name="password" id="password" required placeholder="Masukkan Password">
                                    <div class="show-hide" @error('password') style="padding-right: 10px" @enderror>
                                        <span class="show"></span>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="fxt-btn-fill">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

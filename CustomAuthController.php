<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Session;
use App\Models\Product;
use App\Models\cart;
use App\Models\order;



class CustomAuthController extends Controller
{
    //
    public function login()
    {
     //return "Login";
     return view("auth.account");
    }
    public function registration()
    {
        //return "Registration";
      return view("auth.account");
    }


    public function registerUser1(Request $request)
    {
       // echo 'value posted';
    $request->validate(
        [
            'username'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6|max:12'
        ]
        );
        $User=new User();
        $User->username=$request->username;
        $User->email=$request->email;
        $User->password=Hash::make($request->password);
        $res=$User->save();
        if($res)
        {
        return back()->with('success','You have registered successfully');

        }
        else
        {
         return back()->with('Rfail','Something wrong');
        }
    }

    public function loginUser(Request $request)
    {

        $request->validate(
            [
                
                'email'=>'required|email',
                'password'=>'required|min:6|max:12'
            ]
            );
            $User=User::where('email','=',$request->email)->first();
            if(Session::has('loginId'))
            {
              

                return back()->with('failL','This id is logged in');
                
            }
            if($User)
            {
                if(Hash::check($request->password, $User->password))
                {
                 $request->session()->put('loginId',$User->id);
                 
                 if($User->usertype=='1')
                 {
                     return view('admin.home');
                 }
                 else
                 {
                     return view('auth.home');
                 }

                 //return redirect('products');
                }
                
            
                else
                {
                    return back()->with('fail','Password incorrect');   
                }

            }
            else
            {
               return back()->with('fail','This email is not registered');
            }
            
            
    }

    public function home()
    {
        return view("auth.home");
    }
    public function account()
    {
        return view("auth.account");
    }

    public function logout()
    {
        if(Session::has('loginId'))
        {
            Session::pull('loginId');
           
        }
        return redirect('account');
    }

    public function products()
    {
        $product=product::paginate(12);
        return view('auth.products',compact('product'));
    }

    public function cart()
    {
        $userId = Session::get('loginId');
        $cart=cart::where('user_id','=',$userId)->get();
        return view("auth.cart",compact('cart'));
    }
    public function welcome()
    {
        return view("auth.welcome");
    }

    public function product_details($id)
    {
        $product=product::find($id);
        return view('auth.product_details',compact('product'));
    }

    public function add_cart(Request $request,$id)
    {
        $userId = Session::get('loginId');
        $user=user::find($userId);
        $product=product::find($id);
        $cart=new cart();
        $cart->name=$user->username;
        $cart->email=$user->email;
        $cart->user_id=$user->id;
       //phn r address nei nai

        $cart->product_title=$product->title;

        if($product->discount_price!=null)
        {
            $cart->price=$product->discount_price * $request->quantity;
        }
        else
        {
            $cart->price=$product->price * $request->quantity;
        }
       
        $cart->image=$product->image;
        $cart->product_id=$product->id;

        $cart->quantity=$request->quantity; //input name is quantity

        $cart->save();
        
        return redirect()->back();
    }
    

    public function remove_cart($id)
    {
        $cart=cart::find($id);
        $cart->delete();
        return redirect()->back();
    }

    public function cash_order()
    {
      
     return view('auth.final_order');
    }


    public function cash_confirm(Request $request)
    {
        $userId=Session::get('loginId');
      $data=cart::where('user_id','=',$userId)->get();
      foreach($data as $data)
      {
        $order=new order;
        $order->name=$data->name;
        $order->email=$data->email;
        $order->phone=$request->phone;
       // $order->address=$request->address;
        $order->user_id=$data->user_id;

        $order->product_title=$data->product_title;
        $order->price=$data->price;
         $order->quantity=$data->quantity;
         $order->image=$data->image;
         $order->product_id=$data->product_id;

         $order->payment_status='cash on delivery';
         $order->delivery_status='Processing';

         $order->save();

         $cart_id=$data->id;
         $cart=cart::find($cart_id);
         $cart->delete();

        


      }
      return redirect()->back();
    }
   















   
}

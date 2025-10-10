use Illuminate\Http\Request;
use Fideloper\Proxy\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    protected $proxies = '*'; // Atau bisa pakai array IP Cloudflare

    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}

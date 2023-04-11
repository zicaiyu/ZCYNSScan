import socket


def getdomain(url):
    if url[-1] != "/":
        url = url + "/"
    url = url.replace('http://', '').replace('https://', '')
    url = url + '/'
    url = url.split('/')[0]
    return url


def doCheck(url):
    domain = getdomain(url)
    ip = domain.split(':')[0]
    port = int(domain.split(':')[1])
    try:
        socket.setdefaulttimeout(5)
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        s.connect((ip, port))
        s.send(bytes('envi', 'UTF-8'))
        data = s.recv(1024).decode()
        s.close()
        if 'Environment' in data:
            return url
        else:
            return False
    except:
        return False

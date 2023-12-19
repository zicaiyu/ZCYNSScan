import requests

def save_accessible_domains():
    accessible_domains = []
    domain_list = []
    with open('../../data/HuoXunTongKeJi.txt', 'r') as file:
        for line in file:
            # 去除行尾的换行符并添加到列表中
            domain_list.append(line.rstrip('\n'))
    for domain in domain_list:
        try:
            response = requests.get(f"http://{domain}")
            if response.status_code == 200:
                accessible_domains.append(domain)
                print(domain)
        except requests.exceptions.RequestException:
            pass

    with open('../../data/HuoXunTongKeJi2.txt', "w") as file:
        for domain in accessible_domains:
            file.write(domain + "\n")


if __name__ == '__main__':
    save_accessible_domains()
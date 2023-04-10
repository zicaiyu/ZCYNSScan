import csv


def getCsvSubdomains(domain):
    cols = []
    try:
        with open(f"../OneForAll-master/results/{domain}.csv", "r") as f_in:
            reader = csv.reader(f_in)
            x = next(reader, None)  # skip headers
            if x == None:
                return

            for line in reader:
                if line[12] == None or line[12] == "" or line[12] == "404" or line[12] == 404:
                    continue
                cols.append(line[5])
        cols = list(set(cols))
        f2 = open(f"./results.txt", 'a')
        # f2.seek(0)
        # f2.truncate()
        for col in cols:
            f2.write(col + '\n')
    except:
        return


if __name__ == '__main__':
    domainList = []
    f1 = open('domains.txt', encoding="utf8")
    for line in f1.readlines():
        domain_ = line.strip()
        if domain_:
            if '.gov.' in domain_ or '.edu.' in domain_:
                continue
            domainList.append(domain_)
    f1.close()
    domainList = list(set(domainList))
    for domain in domainList:
        getCsvSubdomains(domain)

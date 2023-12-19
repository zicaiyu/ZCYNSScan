import random


def random_uppercase_letters(string):
    # 随机选择要大写的字母个数（1 到 23）
    num_uppercase = random.randint(1, 23)

    # 将字符串转换为列表，方便对字符进行修改
    letters = list(string)

    # 获取所有字母的索引列表
    letter_indices = [i for i in range(len(letters)) if letters[i].isalpha()]

    # 随机选择要大写的字母的索引
    uppercase_indices = random.sample(letter_indices, num_uppercase)

    # 将选择的字母进行大写
    for index in uppercase_indices:
        letters[index] = letters[index].upper()

    # 将列表转换回字符串并返回
    return ''.join(letters)


# 生成 100 个不同的字符串
generated_strings = set()
while len(generated_strings) < 1000:
    email = "zicaiyushentoutest@qq.com"
    modified_email = random_uppercase_letters(email)
    generated_strings.add(modified_email)

# 输出生成的字符串
for string in generated_strings:
    print(string)
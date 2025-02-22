#!/usr/bin/env python
# -*- coding:UTF-8 -*-
from colorama import init,Fore,Back,Style
init(autoreset=True)

#红色
def red(s):
	print(Fore.RED + s + Fore.RESET)

#绿色
def green(s):
	print(Fore.GREEN + s + Fore.RESET)

#黄色
def yellow(s):
	print(Fore.YELLOW + s + Fore.RESET)

#蓝色
def blue(s):
	print(Fore.BLUE + s + Fore.RESET)

#紫红色
def magenta(s):
	print(Fore.MAGENTA + s + Fore.RESET)

#青色
def cyan(s):
	print(Fore.CYAN + s + Fore.RESET)


import time, math, sys

def is_prime(n: int) -> bool:
    if n < 2:
        return False
    if n % 2 == 0:
        return n == 2
    r = int(n ** 0.5) + 1
    for i in range(3, r, 2):
        if n % i == 0:
            return False
    return True

def count_primes(limit: int) -> int:
    return sum(1 for x in range(limit) if is_prime(x))

LIMIT = 100_000
ITER = 10
start = time.perf_counter()
for _ in range(ITER):
    count_primes(LIMIT)
end = time.perf_counter()
print(f"{end - start:.6f}")
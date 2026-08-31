import React, { useEffect, useState } from "react";
import { UserRoundPlusIcon, UsersRoundIcon } from "lucide-react";
import {
  Avatar,
  AvatarFallback,
  AvatarImage,
} from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";

export interface DashboardUserItem {
  delay: number;
  fallback: string;
  followers: string;
  image: string;
  name: string;
  role: string;
}

const defaultUsers: DashboardUserItem[] = [
  {
    delay: 3000,
    fallback: "SJ",
    followers: "15k",
    image:
      "https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=80&h=80&dpr=2&q=80",
    name: "Sarah Johnson",
    role: "Design Engineer",
  },
  {
    delay: 4000,
    fallback: "MA",
    followers: "8k",
    image:
      "https://images.unsplash.com/photo-1543610892-0b1f7e6d8ac1?w=80&h=80&dpr=2&q=80",
    name: "Mark Bennett Andersson",
    role: "Product Designer",
  },
  {
    delay: 3400,
    fallback: "AR",
    followers: "12k",
    image:
      "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&dpr=2&q=80",
    name: "Alex Rivera",
    role: "UI/UX Designer",
  },
];

function UserCard({ delay, user }: { delay: number; user: DashboardUserItem }) {
  const [isLoaded, setIsLoaded] = useState(false);

  useEffect(() => {
    const timer = setTimeout(() => {
      setIsLoaded(true);
    }, delay);

    return () => clearTimeout(timer);
  }, [delay]);

  if (!isLoaded) {
    return <UserCardSkeleton />;
  }

  return (
    <>
      <Avatar className="h-10 w-10">
        <AvatarImage alt={user.name} src={user.image} />
        <AvatarFallback>{user.fallback}</AvatarFallback>
      </Avatar>
      <div className="flex min-w-0 flex-1 flex-col gap-1">
        <h4 className="line-clamp-1 font-medium text-sm text-slate-800 dark:text-slate-100">{user.name}</h4>
        <div className="flex items-center gap-3 text-muted-foreground text-xs">
          <span className="truncate">{user.role}</span>
          <div className="flex min-w-0 items-center gap-1">
            <UsersRoundIcon className="h-3 w-3 shrink-0" />
            <span className="truncate">
              {user.followers}
              <span className="max-sm:hidden"> followers</span>
            </span>
          </div>
        </div>
      </div>
      <Button size="xs" variant="outline" className="gap-1 shadow-none">
        <UserRoundPlusIcon className="h-3.5 w-3.5" />
        Follow
      </Button>
    </>
  );
}

function UserCardSkeleton() {
  return (
    <>
      <Skeleton className="h-10 w-10 rounded-full shrink-0" />
      <div className="flex flex-1 flex-col gap-1.5">
        <Skeleton className="h-4 w-3/4 max-w-[180px]" />
        <div className="flex items-center gap-2">
          <Skeleton className="h-3 w-1/3 max-w-[80px]" />
          <Skeleton className="h-3 w-1/4 max-w-[60px]" />
        </div>
      </div>
      <Skeleton className="h-7 w-16 shrink-0 rounded-md" />
    </>
  );
}

export function UserListSkeletonDemo({
  users = defaultUsers,
}: {
  users?: DashboardUserItem[];
}) {
  return (
    <div className="flex w-full max-w-sm flex-col gap-5 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
      <div className="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
        <h3 className="text-sm font-semibold text-slate-900 dark:text-white">Aktivitas Anggota Lab</h3>
        <span className="text-[10px] text-muted-foreground bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full">Live</span>
      </div>
      {users.map((user) => (
        <div className="flex items-center gap-3" key={user.fallback + user.name}>
          <UserCard delay={user.delay} user={user} />
        </div>
      ))}
    </div>
  );
}

export default UserListSkeletonDemo;
